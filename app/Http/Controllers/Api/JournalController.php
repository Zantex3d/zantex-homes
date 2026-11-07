<?php

namespace App\Http\Controllers\Api;

use App\Models\JournalEntry;
use App\Services\AccountingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalController
{
    protected AccountingService $service;

    public function __construct()
    {
        $this->service = new AccountingService(auth()->user()->getActiveCompany());
    }

    public function index(Request $request): JsonResponse
    {
        $companyId = $request->user()->preferred_company_id;

        $entries = JournalEntry::where('company_id', $companyId)
            ->with('postedBy', 'lines.account')
            ->orderByDesc('entry_date')
            ->paginate(20);

        return response()->json($entries);
    }

    public function show(JournalEntry $entry): JsonResponse
    {
        $this->authorize('view', $entry);

        return response()->json($entry->load('postedBy', 'lines.account'));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', JournalEntry::class);

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string|max:500',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'numeric|min:0',
            'lines.*.credit' => 'numeric|min:0',
            'lines.*.description' => 'string|max:255',
        ]);

        try {
            $entry = $this->service->createJournalEntry($validated);
            return response()->json($entry->load('lines.account'), 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function post(JournalEntry $entry): JsonResponse
    {
        $this->authorize('update', $entry);

        try {
            $entry = $this->service->postJournalEntry($entry);
            return response()->json($entry);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function reverse(JournalEntry $entry, Request $request): JsonResponse
    {
        $this->authorize('update', $entry);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $reversal = $this->service->reverseJournalEntry($entry, $validated['reason']);
            return response()->json($reversal->load('lines.account'), 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function trialBalance(Request $request): JsonResponse
    {
        $companyId = $request->user()->preferred_company_id;
        $company = auth()->user()->getActiveCompany();
        
        $this->service = new AccountingService($company);
        $balance = $this->service->getTrialBalance();

        return response()->json($balance);
    }
}
