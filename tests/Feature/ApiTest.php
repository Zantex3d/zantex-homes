<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Estate;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Role;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $user;
    protected PropertyType $propertyType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->propertyType = PropertyType::create(['name' => 'Apartment', 'status' => 'active']);

        $this->company = Company::factory()->create([
            'base_currency' => 'NGN',
        ]);

        $this->user = User::factory()->create([
            'preferred_company_id' => $this->company->id,
        ]);

        $role = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Admin',
            'status' => 'active',
        ]);

        UserRole::create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_can_authenticate_with_sanctum(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($response->json('token'));
    }

    public function test_can_fetch_user_data(): void
    {
        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/user', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('name', $this->user->name);
    }

    public function test_can_list_properties(): void
    {
        $estate = Estate::factory()->create([
            'company_id' => $this->company->id,
        ]);

        Property::factory(3)->create([
            'company_id' => $this->company->id,
            'estate_id' => $estate->id,
            'property_type_id' => $this->propertyType->id,
        ]);

        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->getJson('/api/properties', [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.name', fn($value) => is_string($value));
        $this->assertCount(3, $response->json('data'));
    }

    public function test_cannot_access_api_without_auth(): void
    {
        $response = $this->getJson('/api/properties');

        $response->assertStatus(401);
    }

    public function test_cannot_create_property_without_permission(): void
    {
        $estate = Estate::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $userRole = $this->user->userRoles()->first();
        $userRole->role()->update(['name' => 'Viewer']);

        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/properties', [
            'estate_id' => $estate->id,
            'property_code' => 'TEST-001',
            'name' => 'Test Property',
            'property_type_id' => $this->propertyType->id,
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(403);
    }

    public function test_multi_company_isolation(): void
    {
        $otherCompany = Company::factory()->create();
        $otherEstate = Estate::factory()->create(['company_id' => $otherCompany->id]);

        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/properties', [
            'estate_id' => $otherEstate->id,
            'property_code' => 'TEST-001',
            'name' => 'Cross-company test',
            'property_type_id' => $this->propertyType->id,
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422);
    }

    public function test_property_code_must_be_unique(): void
    {
        $estate = Estate::factory()->create(['company_id' => $this->company->id]);

        Property::create([
            'company_id' => $this->company->id,
            'estate_id' => $estate->id,
            'property_code' => 'UNIQUE-001',
            'name' => 'Existing',
            'property_type_id' => $this->propertyType->id,
            'status' => 'active',
        ]);

        $token = $this->user->createToken('test')->plainTextToken;

        $response = $this->postJson('/api/properties', [
            'estate_id' => $estate->id,
            'property_code' => 'UNIQUE-001',
            'name' => 'Duplicate',
            'property_type_id' => $this->propertyType->id,
        ], [
            'Authorization' => "Bearer {$token}",
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('errors.property_code', fn($value) => is_array($value));
    }
}
