<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use App\Models\ExpenseCategory;
use App\Models\State;
use App\Models\LGA;
use App\Models\Currency;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPropertyTypes();
        $this->seedExpenseCategories();
        $this->seedStates();
        $this->seedCurrencies();
    }

    protected function seedPropertyTypes(): void
    {
        $types = [
            'Residential Apartment',
            'Residential House',
            'Commercial Office',
            'Commercial Retail',
            'Warehouse',
            'Industrial',
            'Land',
            'Shortlet',
            'Mixed Use',
            'Student Accommodation',
        ];

        foreach ($types as $type) {
            PropertyType::firstOrCreate([
                'name' => $type,
                'status' => 'active',
            ]);
        }
    }

    protected function seedExpenseCategories(): void
    {
        $categories = [
            'Maintenance & Repairs',
            'Utilities',
            'Property Management',
            'Insurance',
            'Tax & Levies',
            'Security',
            'Cleaning & Sanitation',
            'Staff & Payroll',
            'Marketing & Advertising',
            'Professional Services',
            'Miscellaneous',
        ];

        foreach ($categories as $category) {
            ExpenseCategory::firstOrCreate([
                'name' => $category,
                'status' => 'active',
            ]);
        }
    }

    protected function seedStates(): void
    {
        $states = [
            'Lagos',
            'Abuja',
            'Oyo',
            'Kano',
            'Kaduna',
            'Rivers',
            'Anambra',
            'Edo',
            'Delta',
            'Osun',
            'Imo',
            'Bauchi',
            'Enugu',
            'Borno',
            'Katsina',
            'Kebbi',
            'Kwara',
            'Niger',
            'Plateau',
            'Sokoto',
            'Taraba',
            'Yobe',
            'Zamfara',
            'Adamawa',
            'Abia',
            'Cross River',
            'Ebonyi',
            'Ekiti',
            'Gombe',
            'Jigawa',
            'Kogi',
            'Nasarawa',
            'Ogun',
            'Ondo',
        ];

        foreach ($states as $state) {
            State::firstOrCreate([
                'name' => $state,
                'status' => 'active',
            ]);
        }
    }

    protected function seedCurrencies(): void
    {
        $currencies = [
            ['code' => 'NGN', 'name' => 'Nigerian Naira', 'symbol' => '₦'],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£'],
        ];

        foreach ($currencies as $currency) {
            Currency::firstOrCreate([
                'code' => $currency['code'],
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
                'status' => 'active',
            ]);
        }
    }
}
