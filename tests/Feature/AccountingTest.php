<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use App\Models\User;
use App\Services\AccountingService;

class AccountingTest extends TestCase
{
    protected Company $company;
    protected AccountingService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create([
            'base_currency' => 'NGN',
        ]);

        $this->user = User::factory()->create([
            'preferred_company_id' => $this->company->id,
        ]);

        $this->service = new AccountingService($this->company);
        $this->setupChartOfAccounts();

        $this->actingAs($this->user);
    }

    protected function setupChartOfAccounts(): void
    {
        $accounts = [
            ['code' => '1000', 'name' => 'Cash', 'type' => 'asset'],
            ['code' => '1010', 'name' => 'Bank', 'type' => 'asset'],
            ['code' => '4000', 'name' => 'Rent Income', 'type' => 'revenue'],
            ['code' => '5000', 'name' => 'Maintenance', 'type' => 'expense'],
        ];

        foreach ($accounts as $acc) {
            Account::create([
                'company_id' => $this->company->id,
                'account_code' => $acc['code'],
                'account_name' => $acc['name'],
                'account_type' => $acc['type'],
                'normal_balance' => in_array($acc['type'], ['asset', 'expense']) ? 'debit' : 'credit',
                'status' => 'active',
            ]);
        }
    }

    public function test_can_create_journal_entry(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = $this->service->createJournalEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Test entry',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 10000],
            ],
        ]);

        $this->assertNotNull($entry->id);
        $this->assertEquals('draft', $entry->status);
        $this->assertEquals(2, $entry->lines()->count());
    }

    public function test_journal_entry_must_balance(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = new JournalEntry();
        $entry->company_id = $this->company->id;
        $entry->entry_date = now();
        $entry->description = 'Unbalanced entry';
        $entry->status = 'draft';
        $entry->save();

        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $cash->id,
            'debit' => 10000,
            'credit' => 0,
        ]);

        JournalLine::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $income->id,
            'debit' => 0,
            'credit' => 5000,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->postJournalEntry($entry);
    }

    public function test_can_post_journal_entry(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = $this->service->createJournalEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Test posting',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 10000],
            ],
        ]);

        $posted = $this->service->postJournalEntry($entry);

        $this->assertEquals('posted', $posted->status);
        $this->assertNotNull($posted->posted_at);
    }

    public function test_cannot_post_already_posted_entry(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = $this->service->createJournalEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Test double post',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 10000],
            ],
        ]);

        $this->service->postJournalEntry($entry);

        $this->expectException(\RuntimeException::class);
        $this->service->postJournalEntry($entry->fresh());
    }

    public function test_can_reverse_posted_entry(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = $this->service->createJournalEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Test reversal',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 10000, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 10000],
            ],
        ]);

        $this->service->postJournalEntry($entry);

        $reversal = $this->service->reverseJournalEntry($entry, 'Test reason');

        $this->assertEquals('posted', $reversal->status);
        $this->assertEquals($entry->id, $reversal->reversal_of_id);
        $this->assertEquals(2, $reversal->lines()->count());

        $reversalLines = $reversal->lines()->get();
        $this->assertEquals(0, $reversalLines[0]->debit);
        $this->assertEquals(10000, $reversalLines[0]->credit);
    }

    public function test_account_balance_calculation(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = $this->service->createJournalEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Test balance',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 50000, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 50000],
            ],
        ]);

        $this->service->postJournalEntry($entry);

        $cashBalance = $this->service->getAccountBalance($cash);
        $incomeBalance = $this->service->getAccountBalance($income);

        $this->assertEquals(50000, $cashBalance);
        $this->assertEquals(50000, $incomeBalance);
    }

    public function test_trial_balance_verification(): void
    {
        $cash = Account::where('account_code', '1000')->first();
        $income = Account::where('account_code', '4000')->first();

        $entry = $this->service->createJournalEntry([
            'entry_date' => now()->toDateString(),
            'description' => 'Trial balance test',
            'lines' => [
                ['account_id' => $cash->id, 'debit' => 75000, 'credit' => 0],
                ['account_id' => $income->id, 'debit' => 0, 'credit' => 75000],
            ],
        ]);

        $this->service->postJournalEntry($entry);

        $balance = $this->service->getTrialBalance();

        $this->assertTrue($balance['is_balanced']);
        $this->assertEquals(75000, $balance['total_debit']);
        $this->assertEquals(75000, $balance['total_credit']);
    }
}
