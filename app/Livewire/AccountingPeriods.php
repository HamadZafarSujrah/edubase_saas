<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\AccountingPeriod;
use Illuminate\Support\Facades\Auth;

class AccountingPeriods extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search_id = '';
    public $search_begin = '';
    public $search_end = '';
    public $search_closed = '';

    public $start_date;
    public $end_date;
    public $is_closed = false;
    public $editing_id = null;

    public function resetFields()
    {
        $this->start_date = '';
        $this->end_date = '';
        $this->is_closed = false;
        $this->editing_id = null;
    }

    public function save()
    {
        $this->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($this->editing_id) {
            $period = AccountingPeriod::findOrFail($this->editing_id);
            $period->update([
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_closed' => $this->is_closed,
                'updated_by' => Auth::id(),
            ]);
        } else {
            AccountingPeriod::create([
                'tenant_id' => session('tenant_id'),
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_closed' => false,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        $this->resetFields();
        session()->flash('message', 'Accounting Period saved successfully.');
        $this->dispatch('closeModal');
    }

    public function edit($id)
    {
        $period = AccountingPeriod::findOrFail($id);
        $this->editing_id = $period->id;
        $this->start_date = $period->start_date;
        $this->end_date = $period->end_date;
        $this->is_closed = $period->is_closed;
    }

    public function toggleStatus($id)
    {
        $period = AccountingPeriod::findOrFail($id);
        $period->update(['is_closed' => !$period->is_closed]);
    }

    public function delete($id)
    {
        AccountingPeriod::destroy($id);
        session()->flash('message', 'Period deleted.');
    }

    public function render()
    {
        $query = AccountingPeriod::with(['creator', 'updater'])
            ->when($this->search_id, fn($q) => $q->where('id', 'LIKE', "%{$this->search_id}%"))
            ->when($this->search_begin, fn($q) => $q->where('start_date', 'LIKE', "%{$this->search_begin}%"))
            ->when($this->search_end, fn($q) => $q->where('end_date', 'LIKE', "%{$this->search_end}%"))
            ->when($this->search_closed, fn($q) => $q->where('is_closed', $this->search_closed))
            ->orderBy('start_date', 'desc');

        return view('livewire.accounting-periods', [
            'periods' => $query->paginate(10)
        ])->layout('layouts.app');
    }
}

