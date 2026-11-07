<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Models\Permission;

class PermissionTests extends TestCase
{
    private $tenantUser;
    private $landlordUser;
    private $agentUser;
    private $investorUser;
    private $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();

        // Create roles
        $tenantRole = Role::firstOrCreate(['name' => 'tenant']);
        $landlordRole = Role::firstOrCreate(['name' => 'landlord']);
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $investorRole = Role::firstOrCreate(['name' => 'investor']);

        // Create users with different roles
        $this->tenantUser = User::factory()->create(['company_id' => $this->company->id]);
        $this->tenantUser->roles()->attach($tenantRole);

        $this->landlordUser = User::factory()->create(['company_id' => $this->company->id]);
        $this->landlordUser->roles()->attach($landlordRole);

        $this->agentUser = User::factory()->create(['company_id' => $this->company->id]);
        $this->agentUser->roles()->attach($agentRole);

        $this->investorUser = User::factory()->create(['company_id' => $this->company->id]);
        $this->investorUser->roles()->attach($investorRole);
    }

    public function test_tenant_cannot_access_landlord_portal(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get('/landlord/dashboard');

        $response->assertStatus(403);
    }

    public function test_landlord_cannot_access_tenant_portal(): void
    {
        $response = $this->actingAs($this->landlordUser)
            ->get('/tenant/dashboard');

        $response->assertStatus(403);
    }

    public function test_investor_cannot_access_agent_portal(): void
    {
        $response = $this->actingAs($this->investorUser)
            ->get('/agent/dashboard');

        $response->assertStatus(403);
    }

    public function test_agent_can_access_agent_portal(): void
    {
        $response = $this->actingAs($this->agentUser)
            ->get('/agent/dashboard');

        $response->assertStatus(200);
    }

    public function test_tenant_can_access_tenant_portal(): void
    {
        $response = $this->actingAs($this->tenantUser)
            ->get('/tenant/dashboard');

        $response->assertStatus(200);
    }

    public function test_landlord_can_access_landlord_portal(): void
    {
        $response = $this->actingAs($this->landlordUser)
            ->get('/landlord/dashboard');

        $response->assertStatus(200);
    }

    public function test_investor_can_access_investor_portal(): void
    {
        $response = $this->actingAs($this->investorUser)
            ->get('/investor/dashboard');

        $response->assertStatus(200);
    }
}
