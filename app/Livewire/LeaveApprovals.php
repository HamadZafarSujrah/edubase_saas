<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\LeaveRequest;
use App\Models\Attendance\EmployeeAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class LeaveApprovals extends Component
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

        $query = LeaveRequest::where('tenant_id', $tenantId)
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

        return view('livewire.leave-approvals', [
            'requests' => $query->latest('from_date')->paginate(15),
        ])->layout('layouts.app');
    }

    public function approve($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $leave = LeaveRequest::where('tenant_id', $tenantId)->findOrFail($id);

        DB::transaction(function () use ($leave, $tenantId) {
            $leave->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Reflect the approved leave on the attendance calendar so
            // attendance reports stay consistent with approvals -- without
            // this, an employee could show as "not marked" on approved
            // leave days instead of "leave".
            foreach (CarbonPeriod::create($leave->from_date, $leave->to_date) as $day) {
                EmployeeAttendance::updateOrCreate(
                    ['tenant_id' => $tenantId, 'employee_id' => $leave->employee_id, 'date' => $day->format('Y-m-d')],
                    ['status' => 'leave', 'method' => 'manual', 'remarks' => 'Approved leave', 'marked_by' => Auth::id()]
                );
            }
        });

        session()->flash('message', 'Leave request approved and attendance updated.');
    }

    public function reject($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $leave = LeaveRequest::where('tenant_id', $tenantId)->findOrFail($id);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        session()->flash('message', 'Leave request rejected.');
    }
}
