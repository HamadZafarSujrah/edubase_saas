<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\SalaryAdvance;
use Illuminate\Support\Facades\Auth;

class SalaryAdvanceApprovals extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $status_filter = 'pending';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = SalaryAdvance::where('tenant_id', $tenantId)
            ->with(['employee', 'approvedBy'])
            ->when($this->search, function ($q) {
                $q->whereHas('employee', function ($eq) {
                    $eq->where('first_name', 'like', '%' . $this->search . '%')
                       ->orWhere('last_name', 'like', '%' . $this->search . '%')
                       ->orWhere('emp_no', 'like', '%' . $this->search . '%');
                });
            });

        if ($this->status_filter !== 'all') {
            $query->where('status', $this->status_filter);
        }

        return view('livewire.salary-advance-approvals', [
            'advances' => $query->latest('id')->paginate(15),
        ])->layout('layouts.app');
    }

    public function approve($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $advance = SalaryAdvance::where('tenant_id', $tenantId)->findOrFail($id);

        $advance->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        session()->flash('message', 'Salary advance approved. It will be deducted from this employee\'s next generated salary.');
    }

    public function reject($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $advance = SalaryAdvance::where('tenant_id', $tenantId)->findOrFail($id);

        $advance->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        session()->flash('message', 'Salary advance rejected.');
    }
}
