<?php

namespace App\Livewire\Admin;

use App\Models\JournalEntry;
use App\Services\AccountingService;
use Livewire\Component;
use Livewire\WithPagination;

class JournalList extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortBy = 'entry_date';
    public $sortDir = 'desc';

    protected $queryString = ['search', 'status', 'sortBy', 'sortDir'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $companyId = auth()->user()->preferred_company_id;

        $journals = JournalEntry::where('company_id', $companyId)
            ->when($this->search, fn($q) => 
                $q->where('description', 'like', "%{$this->search}%")
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortBy, $this->sortDir)
            ->with('postedBy', 'lines')
            ->paginate(15);

        return view('livewire.admin.journal-list', [
            'journals' => $journals,
        ]);
    }
}
