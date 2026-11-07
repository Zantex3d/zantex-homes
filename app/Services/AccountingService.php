<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalLine;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use RuntimeException;

class AccountingService
{
    protected Company $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function createJournalEntry(array $data): JournalEntry
    {
        $entry = new JournalEntry();
        $entry->company_id = $this->company->id;
        $entry->public_id = Str::uuid();
        $entry->entry_date = $data['entry_date'] ?? now();
        $entry->description = $data['description'];
        $entry->posted_by_id = auth()->id();
        $entry->status = 'draft';
        $entry->currency = $this->company->base_currency;
        $entry->save();

        $this->addLines($entry, $data['lines'] ?? []);

        return $entry->refresh();
    }

    protected function addLines(JournalEntry $entry, array $lines): void
    {
        foreach ($lines as $line) {
            JournalLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $line['account_id'],
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
                'description' => $line['description'] ?? '',
                'public_id' => Str::uuid(),
            ]);
        }
    }

    public function postJournalEntry(JournalEntry $entry): JournalEntry
    {
        if ($entry->status === 'posted') {
            throw new RuntimeException('Journal entry is already posted.');
        }

        $this->validateBalance($entry);

        $entry->update([
            'status' => 'posted',
            'posted_at' => now(),
        ]);

        return $entry;
    }

    protected function validateBalance(JournalEntry $entry): void
    {
        $lines = $entry->lines;
        $totalDebit = $lines->sum('debit');
        $totalCredit = $lines->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new RuntimeException(
                "Journal entry does not balance. Debit: {$totalDebit}, Credit: {$totalCredit}"
            );
        }
    }

    public function reverseJournalEntry(JournalEntry $entry, string $reason): JournalEntry
    {
        if ($entry->status !== 'posted') {
            throw new RuntimeException('Only posted entries can be reversed.');
        }

        $reversal = new JournalEntry();
        $reversal->company_id = $this->company->id;
        $reversal->public_id = Str::uuid();
        $reversal->entry_date = now();
        $reversal->description = "Reversal of entry {$entry->public_id}: {$reason}";
        $reversal->reversal_of_id = $entry->id;
        $reversal->posted_by_id = auth()->id();
        $reversal->status = 'posted';
        $reversal->currency = $entry->currency;
        $reversal->posted_at = now();
        $reversal->save();

        foreach ($entry->lines as $line) {
            JournalLine::create([
                'journal_entry_id' => $reversal->id,
                'account_id' => $line->account_id,
                'debit' => $line->credit,
                'credit' => $line->debit,
                'description' => $line->description . ' (Reversed)',
                'public_id' => Str::uuid(),
            ]);
        }

        return $reversal;
    }

    public function getAccountBalance(Account $account): float
    {
        $lines = JournalLine::whereHas('journalEntry', fn($q) => 
            $q->where('company_id', $this->company->id)
              ->where('status', 'posted')
        )->where('account_id', $account->id)
         ->get();

        $debit = $lines->sum('debit');
        $credit = $lines->sum('credit');

        if ($account->account_type === 'asset' || $account->account_type === 'expense') {
            return $debit - $credit;
        }

        return $credit - $debit;
    }

    public function getTrialBalance()
    {
        $accounts = Account::where('company_id', $this->company->id)->get();

        $balances = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $this->getAccountBalance($account);

            if ($balance != 0) {
                $balances[$account->id] = [
                    'account_code' => $account->account_code,
                    'name' => $account->account_name,
                    'type' => $account->account_type,
                    'balance' => $balance,
                ];

                if ($balance > 0) {
                    if (in_array($account->account_type, ['asset', 'expense'])) {
                        $totalDebit += $balance;
                    } else {
                        $totalCredit += $balance;
                    }
                } else {
                    if (in_array($account->account_type, ['asset', 'expense'])) {
                        $totalCredit += abs($balance);
                    } else {
                        $totalDebit += abs($balance);
                    }
                }
            }
        }

        return [
            'balances' => $balances,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit,
            'is_balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }
}
