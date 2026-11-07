<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\WebhookEvent;
use App\Models\Journal;
use App\Models\AuditLog;

class PaystackWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $signature = $request->header('X-Paystack-Signature');
        $payload = $request->getContent();

        // Verify signature
        if (!$this->verifySignature($payload, $signature)) {
            return response('Unauthorized', 401);
        }

        $event = json_decode($payload, true);

        try {
            // Check for idempotency - prevent duplicate processing
            if (WebhookEvent::where('event_id', $event['data']['reference'] ?? null)
                ->where('status', 'processed')
                ->exists()) {
                return response('Already processed', 200);
            }

            // Record webhook event
            $webhookEvent = WebhookEvent::create([
                'event_type' => $event['event'] ?? 'unknown',
                'event_id' => $event['data']['reference'] ?? null,
                'payload' => $payload,
                'status' => 'processing',
            ]);

            // Handle different event types
            match ($event['event'] ?? null) {
                'charge.success' => $this->handleChargeSuccess($event['data']),
                'charge.failed' => $this->handleChargeFailed($event['data']),
                'transfer.success' => $this->handleTransferSuccess($event['data']),
                'transfer.failed' => $this->handleTransferFailed($event['data']),
                default => null,
            };

            $webhookEvent->update(['status' => 'processed']);

            AuditLog::create([
                'action' => 'webhook_received',
                'model_type' => 'WebhookEvent',
                'model_id' => $webhookEvent->id,
                'description' => "Processed {$event['event']} webhook",
                'user_id' => null,
                'status' => 'completed',
            ]);

            return response('OK', 200);
        } catch (\Exception $e) {
            WebhookEvent::where('id', $webhookEvent->id ?? null)
                ->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

            AuditLog::create([
                'action' => 'webhook_error',
                'model_type' => 'WebhookEvent',
                'model_id' => $webhookEvent->id ?? 0,
                'description' => "Webhook error: {$e->getMessage()}",
                'user_id' => null,
                'status' => 'failed',
            ]);

            return response('Processing error', 500);
        }
    }

    private function verifySignature(string $payload, ?string $signature): bool
    {
        $secret = config('services.paystack.secret');
        $hash = hash_hmac('sha512', $payload, $secret);
        return hash_equals($hash, $signature ?? '');
    }

    private function handleChargeSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (!$reference) return;

        // Find payment by Paystack reference
        $payment = Payment::where('paystack_reference', $reference)->first();
        if (!$payment) return;

        $payment->update(['status' => 'approved']);

        // Post journal entry: Debit Bank, Credit Receivable
        Journal::create([
            'company_id' => $payment->company_id ?? 1,
            'date' => now(),
            'description' => "Payment received from {$payment->payer->name}",
            'reference' => $reference,
            'debit_account_id' => 100, // Cash/Bank
            'debit_amount' => $payment->amount,
        ]);

        Journal::create([
            'company_id' => $payment->company_id ?? 1,
            'date' => now(),
            'description' => "Payment received from {$payment->payer->name}",
            'reference' => $reference,
            'credit_account_id' => 120, // Accounts Receivable
            'credit_amount' => $payment->amount,
        ]);
    }

    private function handleChargeFailed(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (!$reference) return;

        $payment = Payment::where('paystack_reference', $reference)->first();
        if ($payment) {
            $payment->update(['status' => 'rejected']);
        }
    }

    private function handleTransferSuccess(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (!$reference) return;

        $payout = Payout::where('paystack_reference', $reference)->first();
        if ($payout) {
            $payout->update(['status' => 'completed']);
        }
    }

    private function handleTransferFailed(array $data): void
    {
        $reference = $data['reference'] ?? null;
        if (!$reference) return;

        $payout = Payout::where('paystack_reference', $reference)->first();
        if ($payout) {
            $payout->update(['status' => 'failed']);
        }
    }
}
