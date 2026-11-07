<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Lease;
use App\Models\AuditLog;

class LeaseExpiryNoticeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Find leases expiring in 30 days
        $expiringLeases = Lease::whereDate('end_date', now()->addDays(30)->toDateString())
            ->with('tenant', 'landlord')
            ->get();

        foreach ($expiringLeases as $lease) {
            // Send to tenant
            dispatch(new SendEmailJob(
                recipient: $lease->tenant->email,
                subject: 'Lease Expiry Notice',
                body: "Your lease agreement expires on " . $lease->end_date->format('M d, Y') . ". Please contact your landlord to discuss renewal or termination.",
                modelType: 'Lease',
                modelId: $lease->id
            ));

            // Send to landlord
            dispatch(new SendEmailJob(
                recipient: $lease->landlord->email,
                subject: 'Lease Expiry Notice',
                body: "The lease for " . $lease->tenant->name . " expires on " . $lease->end_date->format('M d, Y') . ".",
                modelType: 'Lease',
                modelId: $lease->id
            ));
        }

        AuditLog::create([
            'action' => 'lease_expiry_notice',
            'model_type' => 'Lease',
            'model_id' => 0,
            'description' => "Sent lease expiry notices for " . $expiringLeases->count() . " leases",
            'user_id' => null,
            'status' => 'completed',
        ]);
    }
}
