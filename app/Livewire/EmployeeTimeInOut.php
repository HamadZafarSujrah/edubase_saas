<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeTimeInOut extends Component
{
    public $campus_id = '';
    public $department_id = '';
    public $date;

    // [employee_id => ['status' => ..., 'time_in' => ..., 'time_out' => ..., 'remarks' => ...]]
    public $roster = [];
    public $loaded = false;

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatedCampusId() { $this->loaded = false; }
    public function updatedDepartmentId() { $this->loaded = false; }
    public function updatedDate() { $this->loaded = false; }

    public function loadRoster()
    {
        if (!$this->date) {
            session()->flash('error', 'Select a date first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $employeesQuery = Employee::where('tenant_id', $tenantId)->where('status', 'active');
        if ($this->campus_id) {
            $employeesQuery->where('campus_id', $this->campus_id);
        }
        if ($this->department_id) {
            $employeesQuery->where('department_id', $this->department_id);
        }
        $employees = $employeesQuery->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'emp_no']);

        $existing = EmployeeAttendance::where('tenant_id', $tenantId)
            ->where('date', $this->date)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        $roster = [];
        foreach ($employees as $e) {
            $mark = $existing->get($e->id);
            $roster[$e->id] = [
                'name' => trim($e->first_name . ' ' . $e->last_name),
                'emp_no' => $e->emp_no,
                'status' => $mark->status ?? 'present',
                'time_in' => $mark && $mark->time_in ? \Carbon\Carbon::parse($mark->time_in)->format('H:i') : '',
                'time_out' => $mark && $mark->time_out ? \Carbon\Carbon::parse($mark->time_out)->format('H:i') : '',
                'remarks' => $mark->remarks ?? '',
                'locked' => (bool) ($mark->is_locked ?? false),
            ];
        }

        $this->roster = $roster;
        $this->loaded = true;
    }

    public function markAll($status)
    {
        foreach ($this->roster as $id => $entry) {
            if (!empty($entry['locked'])) {
                continue;
            }
            $this->roster[$id]['status'] = $status;
        }
    }

    public function save()
    {
        if (empty($this->roster)) {
            session()->flash('error', 'Load a roster first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $lockedCount = 0;

        DB::transaction(function () use ($tenantId, &$lockedCount) {
            foreach ($this->roster as $employeeId => $entry) {
                if (!empty($entry['locked'])) {
                    $lockedCount++;
                    continue;
                }

                EmployeeAttendance::updateOrCreate(
                    ['tenant_id' => $tenantId, 'employee_id' => $employeeId, 'date' => $this->date],
                    [
                        'status' => $entry['status'],
                        'time_in' => $entry['time_in'] ?: null,
                        'time_out' => $entry['time_out'] ?: null,
                        'method' => 'manual',
                        'remarks' => $entry['remarks'] ?: null,
                        'marked_by' => Auth::id(),
                    ]
                );
            }
        });

        $savedCount = count($this->roster) - $lockedCount;
        $message = 'Attendance saved for ' . $savedCount . ' employee(s) on ' . \Carbon\Carbon::parse($this->date)->format('d-M-Y') . '.';
        if ($lockedCount > 0) {
            $message .= ' ' . $lockedCount . ' employee(s) skipped because their attendance for this date is locked.';
        }
        session()->flash('message', $message);
        $this->loadRoster();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.employee-time-in-out', [
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
