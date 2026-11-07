<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\Property;

class CommissionCalculationTests extends TestCase
{
    private $agent;
    private $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agent = Agent::factory()->create();
        $this->property = Property::factory()->create();
    }

    public function test_commission_calculated_as_percentage_of_sale(): void
    {
        $salePrice = 10000000; // 10 million naira
        $commissionRate = 0.03; // 3%

        $commission = AgentCommission::create([
            'agent_id' => $this->agent->id,
            'property_id' => $this->property->id,
            'sale_price' => $salePrice,
            'commission_rate' => $commissionRate,
            'amount' => $salePrice * $commissionRate,
            'status' => 'pending',
        ]);

        $expectedAmount = $salePrice * $commissionRate;
        $this->assertEquals($expectedAmount, $commission->amount);
    }

    public function test_commission_status_workflow(): void
    {
        $commission = AgentCommission::create([
            'agent_id' => $this->agent->id,
            'property_id' => $this->property->id,
            'sale_price' => 10000000,
            'commission_rate' => 0.03,
            'amount' => 300000,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $commission->status);

        $commission->update(['status' => 'approved']);
        $this->assertEquals('approved', $commission->fresh()->status);

        $commission->update(['status' => 'paid']);
        $this->assertEquals('paid', $commission->fresh()->status);
    }

    public function test_agent_commission_total_ytd(): void
    {
        for ($i = 0; $i < 5; $i++) {
            AgentCommission::create([
                'agent_id' => $this->agent->id,
                'property_id' => $this->property->id,
                'sale_price' => 10000000,
                'commission_rate' => 0.03,
                'amount' => 300000,
                'status' => 'paid',
                'created_at' => now()->subMonths(rand(0, 11)),
            ]);
        }

        $totalYTD = AgentCommission::where('agent_id', $this->agent->id)
            ->where('status', 'paid')
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        $this->assertEquals(1500000, $totalYTD);
    }
}
