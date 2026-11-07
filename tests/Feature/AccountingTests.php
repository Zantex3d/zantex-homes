<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Journal;
use App\Models\Account;
use App\Models\Company;

class AccountingTests extends TestCase
{
    private $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::factory()->create();
    }

    public function test_journal_posting_maintains_balance(): void
    {
        $debitAccount = Account::factory()->create([
            'type' => 'asset',
            'company_id' => $this->company->id,
        ]);

        $creditAccount = Account::factory()->create([
            'type' => 'liability',
            'company_id' => $this->company->id,
        ]);

        // Create balanced journal entry
        $amount = 1000000; // 1 million naira

        Journal::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Test entry',
            'debit_account_id' => $debitAccount->id,
            'debit_amount' => $amount,
        ]);

        Journal::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Test entry',
            'credit_account_id' => $creditAccount->id,
            'credit_amount' => $amount,
        ]);

        // Verify: Total debits = Total credits
        $totalDebits = Journal::where('company_id', $this->company->id)->sum('debit_amount');
        $totalCredits = Journal::where('company_id', $this->company->id)->sum('credit_amount');

        $this->assertEquals($totalDebits, $totalCredits);
    }

    public function test_double_entry_prevents_unbalanced_entries(): void
    {
        $this->expectException(\Exception::class);

        // Attempt to create unbalanced entry (should fail in real implementation)
        Journal::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Unbalanced entry',
            'debit_amount' => 1000000,
            'credit_amount' => 500000, // Unbalanced!
        ]);
    }

    public function test_account_balances_calculated_correctly(): void
    {
        $account = Account::factory()->create([
            'type' => 'asset',
            'company_id' => $this->company->id,
        ]);

        // Create multiple journal entries
        Journal::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Entry 1',
            'debit_account_id' => $account->id,
            'debit_amount' => 500000,
        ]);

        Journal::create([
            'company_id' => $this->company->id,
            'date' => now(),
            'description' => 'Entry 2',
            'debit_account_id' => $account->id,
            'debit_amount' => 300000,
        ]);

        // Calculate balance
        $balance = $account->getBalance();

        $this->assertEquals(800000, $balance);
    }
}
