<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\SalaryPlan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryPlanApprovals extends Component
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

        $query = SalaryPlan::where('tenant_id', $tenantId)
            ->with(['employee', 'allowances', 'approvedBy'])
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

        return view('livewire.salary-plan-approvals', [
            'plans' => $query->latest('id')->paginate(15),
        ])->layout('layouts.app');
    }

    public function approve($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $plan = SalaryPlan::where('tenant_id', $tenantId)->findOrFail($id);

        DB::transaction(function () use ($plan, $tenantId) {
            // Only one active plan governs an employee's pay at a time -- an
            // increment is just a new plan version, so approving it retires
            // whichever plan was previously active for this employee.
            SalaryPlan::where('tenant_id', $tenantId)
                ->where('employee_id', $plan->employee_id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $plan->update([
                'status' => 'approved',
                'is_active' => true,
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        session()->flash('message', 'Salary plan approved and is now active.');
    }

    public function reject($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $plan = SalaryPlan::where('tenant_id', $tenantId)->findOrFail($id);

        $plan->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        session()->flash('message', 'Salary plan rejected.');
    }
}
