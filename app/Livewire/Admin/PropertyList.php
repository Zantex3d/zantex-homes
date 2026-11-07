<?php

namespace App\Livewire\Admin;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class PropertyList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'name';
    public $sortDir = 'asc';

    protected $queryString = ['search', 'sortBy', 'sortDir'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setSortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
    }

    public function render()
    {
        $companyId = auth()->user()->preferred_company_id;

        $properties = Property::where('company_id', $companyId)
            ->when($this->search, fn($q) => 
                $q->where('property_code', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
            )
            ->orderBy($this->sortBy, $this->sortDir)
            ->with('estate', 'propertyType')
            ->paginate(10);

        return view('livewire.admin.property-list', [
            'properties' => $properties,
        ]);
    }
}
