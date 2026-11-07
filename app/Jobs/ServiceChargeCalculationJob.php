<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Unit;
use App\Models\ServiceCharge;
use App\Models\Journal;
use App\Models\AuditLog;

class ServiceChargeCalculationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get all occupied units
        $units = Unit::where('status', 'occupied')->with('property')->get();

        foreach ($units as $unit) {
            // Get service charge configuration
            $rate = $unit->property->service_charge_rate ?? 0.05; // 5% default
            $amount = ($unit->monthly_rent ?? 0) * $rate;

            if ($amount > 0) {
                // Create service charge entry
                $charge = ServiceCharge::create([
                    'unit_id' => $unit->id,
                    'property_id' => $unit->property_id,
                    'tenant_id' => $unit->lease?->tenant_id,
                    'amount' => $amount,
                    'due_date' => now()->addDays(7),
                    'status' => 'pending',
                    'period' => 'monthly',
                ]);

                // Post journal entry (double-entry accounting)
                Journal::create([
                    'company_id' => $unit->property->company_id,
                    'date' => now(),
                    'description' => "Service charge for unit {$unit->name}",
                    'reference' => "SC-{$charge->id}",
                    'debit_account_id' => 120, // Accounts Receivable
                    'debit_amount' => $amount,
                ]);

                Journal::create([
                    'company_id' => $unit->property->company_id,
                    'date' => now(),
                    'description' => "Service charge revenue for unit {$unit->name}",
                    'reference' => "SC-{$charge->id}",
                    'credit_account_id' => 410, // Service Charge Revenue
                    'credit_amount' => $amount,
                ]);
            }
        }

        AuditLog::create([
            'action' => 'service_charge_calculation',
            'model_type' => 'ServiceCharge',
            'model_id' => 0,
            'description' => "Calculated service charges for " . $units->count() . " units",
            'user_id' => null,
            'status' => 'completed',
        ]);
    }
}
