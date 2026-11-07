<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Role;
use App\Models\Permission;
use App\Models\UserRole;

class PermissionTest extends TestCase
{
    protected Company $company;
    protected User $user;
    protected Role $adminRole;
    protected Role $managerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create([
            'base_currency' => 'NGN',
            'timezone' => 'Africa/Lagos',
        ]);

        $this->user = User::factory()->create([
            'preferred_company_id' => $this->company->id,
        ]);

        $this->setupRolesAndPermissions();
    }

    protected function setupRolesAndPermissions(): void
    {
        $permissions = [
            'view_properties', 'create_property', 'edit_property', 'delete_property',
            'view_accounting', 'create_journal', 'post_journal', 'reverse_journal',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $this->adminRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Admin',
            'status' => 'active',
        ]);

        $this->managerRole = Role::create([
            'company_id' => $this->company->id,
            'name' => 'Manager',
            'status' => 'active',
        ]);

        $this->adminRole->permissions()->attach(Permission::pluck('id'));
        $this->managerRole->permissions()->attach(
            Permission::whereIn('name', ['view_properties', 'view_accounting'])->pluck('id')
        );
    }

    public function test_admin_has_all_permissions(): void
    {
        UserRole::create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'role_id' => $this->adminRole->id,
        ]);

        $this->assertTrue($this->user->hasPermission('view_properties'));
        $this->assertTrue($this->user->hasPermission('create_property'));
        $this->assertTrue($this->user->hasPermission('post_journal'));
        $this->assertTrue($this->user->hasPermission('delete_property'));
    }

    public function test_manager_has_limited_permissions(): void
    {
        UserRole::create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'role_id' => $this->managerRole->id,
        ]);

        $this->assertTrue($this->user->hasPermission('view_properties'));
        $this->assertFalse($this->user->hasPermission('create_property'));
        $this->assertTrue($this->user->hasPermission('view_accounting'));
        $this->assertFalse($this->user->hasPermission('post_journal'));
    }

    public function test_user_without_role_has_no_permissions(): void
    {
        $this->assertFalse($this->user->hasPermission('view_properties'));
        $this->assertFalse($this->user->hasPermission('create_property'));
    }

    public function test_permissions_are_company_scoped(): void
    {
        $otherCompany = Company::factory()->create();

        UserRole::create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'role_id' => $this->adminRole->id,
        ]);

        $this->assertTrue($this->user->hasPermission('view_properties', $this->company->id));
        $this->assertFalse($this->user->hasPermission('view_properties', $otherCompany->id));
    }

    public function test_role_can_grant_permission(): void
    {
        $permission = Permission::where('name', 'create_property')->first();
        $this->managerRole->grantPermission($permission);

        UserRole::create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'role_id' => $this->managerRole->id,
        ]);

        $this->assertTrue($this->user->hasPermission('create_property'));
    }

    public function test_role_can_revoke_permission(): void
    {
        $permission = Permission::where('name', 'view_properties')->first();
        $this->managerRole->revokePermission($permission);

        UserRole::create([
            'user_id' => $this->user->id,
            'company_id' => $this->company->id,
            'role_id' => $this->managerRole->id,
        ]);

        $this->assertFalse($this->user->hasPermission('view_properties'));
    }
}
