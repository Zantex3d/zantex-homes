<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\RentInvoice;
use App\Models\AuditLog;

class RentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Find invoices due in 7 days
        $invoicesDue = RentInvoice::where('status', 'pending')
            ->whereDate('due_date', now()->addDays(7)->toDateString())
            ->with('tenant')
            ->get();

        foreach ($invoicesDue as $invoice) {
            dispatch(new SendEmailJob(
                recipient: $invoice->tenant->email,
                subject: 'Rent Payment Reminder',
                body: "Your rent of ₦" . number_format($invoice->amount, 2) . " is due on " . $invoice->due_date->format('M d, Y'),
                modelType: 'RentInvoice',
                modelId: $invoice->id
            ));
        }

        AuditLog::create([
            'action' => 'rent_reminder',
            'model_type' => 'RentInvoice',
            'model_id' => 0,
            'description' => "Sent rent reminders to " . $invoicesDue->count() . " tenants",
            'user_id' => null,
            'status' => 'completed',
        ]);
    }
}
