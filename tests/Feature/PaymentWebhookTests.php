<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Payment;
use App\Models\WebhookEvent;
use Illuminate\Http\Request;

class PaymentWebhookTests extends TestCase
{
    private $paystackSecret = 'pk_test_fake_secret';

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.paystack.secret' => $this->paystackSecret]);
    }

    public function test_webhook_signature_verification(): void
    {
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => 'test_ref_123',
                'amount' => 500000,
            ],
        ]);

        $signature = hash_hmac('sha512', $payload, $this->paystackSecret);

        $response = $this->postJson('/api/webhook/paystack', [], [
            'X-Paystack-Signature' => $signature,
            'Content-Type' => 'application/json',
        ]);

        // Should not return 401 (Invalid signature)
        $this->assertNotEquals(401, $response->status());
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => 'test_ref_123'],
        ]);

        $response = $this->postJson('/api/webhook/paystack', [], [
            'X-Paystack-Signature' => 'invalid_signature',
            'Content-Type' => 'application/json',
        ]);

        $this->assertEquals(401, $response->status());
    }

    public function test_webhook_prevents_duplicate_processing(): void
    {
        WebhookEvent::create([
            'event_type' => 'charge.success',
            'event_id' => 'test_ref_123',
            'payload' => '{}',
            'status' => 'processed',
        ]);

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => 'test_ref_123'],
        ]);

        $signature = hash_hmac('sha512', $payload, $this->paystackSecret);

        $response = $this->postJson('/api/webhook/paystack', json_decode($payload, true), [
            'X-Paystack-Signature' => $signature,
        ]);

        // Should return 200 but indicate already processed
        $this->assertEquals(200, $response->status());
    }

    public function test_webhook_updates_payment_on_success(): void
    {
        $payer = User::factory()->create();
        
        $payment = Payment::create([
            'payer_id' => $payer->id,
            'payee_id' => 2,
            'amount' => 500000,
            'status' => 'pending',
            'paystack_reference' => 'test_ref_456',
            'payment_method' => 'card',
        ]);

        $payload = json_encode([
            'event' => 'charge.success',
            'data' => [
                'reference' => 'test_ref_456',
                'amount' => 500000,
            ],
        ]);

        $signature = hash_hmac('sha512', $payload, $this->paystackSecret);

        $response = $this->postJson('/api/webhook/paystack', json_decode($payload, true), [
            'X-Paystack-Signature' => $signature,
        ]);

        $payment->refresh();
        $this->assertEquals('approved', $payment->status);
    }
}
