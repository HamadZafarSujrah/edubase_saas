<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\JournalEntry;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;

class JournalInquiry extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $start_date = '';
    public $end_date   = '';
    public $search     = '';
    public $filter_campus = '';

    // Expand/collapse a single entry's line items inline
    public $expanded_entry_id = null;

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');
    }

    public function updated() { $this->resetPage(); }

    public function toggleExpand($entryId)
    {
        $this->expanded_entry_id = $this->expanded_entry_id === $entryId ? null : $entryId;
    }

    public function resetFilters()
    {
        $this->start_date    = date('Y-m-01');
        $this->end_date      = date('Y-m-d');
        $this->search        = '';
        $this->filter_campus = '';
        $this->resetPage();
    }

    protected function buildQuery()
    {
        $query = JournalEntry::with(['items.account', 'campus', 'creator']);

        if ($this->start_date) {
            $query->where('transaction_date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->where('transaction_date', '<=', $this->end_date);
        }
        if ($this->filter_campus) {
            $query->where('campus_id', $this->filter_campus);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('voucher_no', 'like', '%' . $this->search . '%')
                  ->orWhere('narration', 'like', '%' . $this->search . '%')
                  ->orWhere('reference', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('transaction_date', 'desc')->orderBy('id', 'desc');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.journal-inquiry', [
            'entries'  => $this->buildQuery()->paginate(20),
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
        ])->layout('layouts.app');
    }
}
