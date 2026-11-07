<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Branch;
use App\Models\Estate;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Account;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use App\Models\UserRole;
use App\Models\UserScope;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::create([
            'public_id' => Str::uuid(),
            'legal_name' => 'DEMO - Zantex Properties Ltd',
            'trading_name' => 'Zantex Demo',
            'registration_no' => 'RC1234567',
            'tax_id' => 'TAX-001',
            'base_currency' => 'NGN',
            'timezone' => 'Africa/Lagos',
            'status' => 'active',
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'public_id' => Str::uuid(),
            'name' => 'Head Office',
            'location' => 'Lagos',
            'status' => 'active',
        ]);

        $estate = Estate::create([
            'company_id' => $company->id,
            'branch_id' => $branch->id,
            'public_id' => Str::uuid(),
            'name' => 'DEMO - Victoria Island Estate',
            'location' => 'Victoria Island, Lagos',
            'status' => 'active',
        ]);

        $property = Property::create([
            'company_id' => $company->id,
            'estate_id' => $estate->id,
            'public_id' => Str::uuid(),
            'property_code' => 'DEMO-VIE-001',
            'name' => 'Luxury Apartment Complex',
            'property_type_id' => 1,
            'status' => 'active',
        ]);

        for ($i = 1; $i <= 5; $i++) {
            Unit::create([
                'company_id' => $company->id,
                'property_id' => $property->id,
                'public_id' => Str::uuid(),
                'unit_number' => "UNIT-{$i}",
                'unit_type' => 'Apartment',
                'bedrooms' => 3,
                'bathrooms' => 2,
                'status' => 'available',
            ]);
        }

        $this->setupChartOfAccounts($company);
        $this->setupPermissions($company);
        $this->setupRoles($company);
        $this->createDemoUser($company);
    }

    protected function setupChartOfAccounts(Company $company): void
    {
        $accounts = [
            // Assets
            ['account_code' => '1000', 'account_name' => 'Cash', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1010', 'account_name' => 'Bank Account', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1020', 'account_name' => 'Accounts Receivable', 'account_type' => 'asset', 'normal_balance' => 'debit'],
            ['account_code' => '1100', 'account_name' => 'Property', 'account_type' => 'asset', 'normal_balance' => 'debit'],

            // Liabilities
            ['account_code' => '2000', 'account_name' => 'Accounts Payable', 'account_type' => 'liability', 'normal_balance' => 'credit'],
            ['account_code' => '2010', 'account_name' => 'Rent Payable', 'account_type' => 'liability', 'normal_balance' => 'credit'],

            // Equity
            ['account_code' => '3000', 'account_name' => 'Owner Equity', 'account_type' => 'equity', 'normal_balance' => 'credit'],

            // Revenue
            ['account_code' => '4000', 'account_name' => 'Rent Income', 'account_type' => 'revenue', 'normal_balance' => 'credit'],
            ['account_code' => '4010', 'account_name' => 'Service Charge Income', 'account_type' => 'revenue', 'normal_balance' => 'credit'],

            // Expenses
            ['account_code' => '5000', 'account_name' => 'Maintenance Expense', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ['account_code' => '5010', 'account_name' => 'Utility Expense', 'account_type' => 'expense', 'normal_balance' => 'debit'],
            ['account_code' => '5020', 'account_name' => 'Management Fees', 'account_type' => 'expense', 'normal_balance' => 'debit'],
        ];

        foreach ($accounts as $account) {
            Account::create(array_merge($account, [
                'company_id' => $company->id,
                'public_id' => Str::uuid(),
                'status' => 'active',
            ]));
        }
    }

    protected function setupPermissions(Company $company): void
    {
        $permissions = [
            'view_properties', 'create_property', 'edit_property', 'delete_property',
            'view_tenants', 'create_tenant', 'edit_tenant', 'delete_tenant',
            'view_leases', 'create_lease', 'edit_lease', 'delete_lease',
            'view_accounting', 'create_journal', 'post_journal', 'reverse_journal',
            'view_payments', 'create_payment', 'approve_payment',
            'view_reports', 'generate_reports',
            'manage_users', 'manage_roles', 'manage_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'status' => 'active',
            ]);
        }
    }

    protected function setupRoles(Company $company): void
    {
        $roles = ['Admin', 'Manager', 'Accountant', 'Tenant', 'Agent'];
        $permissions = Permission::all();

        foreach ($roles as $roleName) {
            $role = Role::create([
                'company_id' => $company->id,
                'public_id' => Str::uuid(),
                'name' => $roleName,
                'description' => "{$roleName} role for {$company->trading_name}",
                'status' => 'active',
            ]);

            if ($roleName === 'Admin') {
                $role->permissions()->attach($permissions);
            } elseif ($roleName === 'Manager') {
                $role->permissions()->attach(
                    $permissions->whereIn('name', [
                        'view_properties', 'create_property', 'edit_property',
                        'view_tenants', 'view_leases',
                        'view_accounting', 'view_payments', 'view_reports',
                    ])->pluck('id')
                );
            } elseif ($roleName === 'Accountant') {
                $role->permissions()->attach(
                    $permissions->whereIn('name', [
                        'view_accounting', 'create_journal', 'post_journal', 'reverse_journal',
                        'view_payments', 'create_payment',
                        'view_reports', 'generate_reports',
                    ])->pluck('id')
                );
            }
        }
    }

    protected function createDemoUser(Company $company): void
    {
        $user = User::create([
            'public_id' => Str::uuid(),
            'name' => 'Demo Administrator',
            'email' => 'admin@demo.zantex.local',
            'password' => bcrypt('password'),
            'preferred_company_id' => $company->id,
            'status' => 'active',
        ]);

        $role = $company->roles()->where('name', 'Admin')->first();

        UserRole::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'role_id' => $role->id,
        ]);

        UserScope::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
        ]);
    }
}
