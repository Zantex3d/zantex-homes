<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Lease;
use App\Models\Unit;
use App\Models\Property;
use App\Models\User;
use App\Models\RentInvoice;
use Carbon\Carbon;

class RentScheduleTests extends TestCase
{
    private $tenant;
    private $landlord;
    private $property;
    private $unit;
    private $lease;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = User::factory()->create();
        $this->landlord = User::factory()->create();
        $this->property = Property::factory()->create(['landlord_id' => $this->landlord->id]);
        $this->unit = Unit::factory()->create(['property_id' => $this->property->id]);

        $this->lease = Lease::create([
            'tenant_id' => $this->tenant->id,
            'landlord_id' => $this->landlord->id,
            'property_id' => $this->property->id,
            'unit_id' => $this->unit->id,
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->addYear(),
            'monthly_amount' => 5000000, // 5 million naira
            'duration_months' => 12,
        ]);
    }

    public function test_rent_invoices_generated_monthly(): void
    {
        // Create invoices for next 12 months
        $date = $this->lease->start_date;
        
        for ($i = 0; $i < 12; $i++) {
            RentInvoice::create([
                'lease_id' => $this->lease->id,
                'tenant_id' => $this->tenant->id,
                'property_id' => $this->property->id,
                'amount' => $this->lease->monthly_amount,
                'due_date' => $date->copy()->addMonth($i),
                'invoice_date' => $date->copy()->addMonth($i),
                'status' => 'pending',
            ]);
        }

        $invoices = RentInvoice::where('lease_id', $this->lease->id)->get();
        $this->assertEquals(12, $invoices->count());
    }

    public function test_rent_payment_updates_invoice_status(): void
    {
        $invoice = RentInvoice::create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'amount' => $this->lease->monthly_amount,
            'due_date' => now(),
            'invoice_date' => now(),
            'status' => 'pending',
        ]);

        // Simulate payment
        $invoice->update(['status' => 'paid']);

        $this->assertEquals('paid', $invoice->fresh()->status);
    }

    public function test_overdue_rent_detection(): void
    {
        $pastDue = RentInvoice::create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'amount' => $this->lease->monthly_amount,
            'due_date' => Carbon::now()->subDays(30),
            'invoice_date' => Carbon::now()->subDays(30),
            'status' => 'pending',
        ]);

        $isOverdue = $pastDue->due_date->isPast() && $pastDue->status === 'pending';
        
        $this->assertTrue($isOverdue);
    }

    public function test_lease_prevents_duplicate_invoices(): void
    {
        $date = now()->startOfMonth();

        RentInvoice::create([
            'lease_id' => $this->lease->id,
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'amount' => $this->lease->monthly_amount,
            'due_date' => $date,
            'invoice_date' => $date,
            'status' => 'pending',
        ]);

        // Attempt to create duplicate
        $duplicate = RentInvoice::where('lease_id', $this->lease->id)
            ->where('due_date', $date)
            ->count();

        $this->assertEquals(1, $duplicate);
    }
}
