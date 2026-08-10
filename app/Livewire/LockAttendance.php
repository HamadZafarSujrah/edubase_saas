<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LockAttendance extends Component
{
    public $campus_id = '';
    public $department_id = '';
    public $date;

    // [employee_id => ['name' => ..., 'emp_no' => ..., 'status' => ..., 'locked' => bool]]
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
                'locked' => (bool) ($mark->is_locked ?? false),
            ];
        }

        $this->roster = $roster;
        $this->loaded = true;
    }

    public function lockDay()
    {
        if (empty($this->roster)) {
            session()->flash('error', 'Load a roster first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        DB::transaction(function () use ($tenantId) {
            foreach ($this->roster as $employeeId => $entry) {
                EmployeeAttendance::updateOrCreate(
                    ['tenant_id' => $tenantId, 'employee_id' => $employeeId, 'date' => $this->date],
                    [
                        'status' => $entry['status'],
                        'is_locked' => true,
                        'locked_by' => Auth::id(),
                        'locked_at' => now(),
                    ]
                );
            }
        });

        session()->flash('message', 'Attendance locked for ' . count($this->roster) . ' employee(s) on ' . \Carbon\Carbon::parse($this->date)->format('d-M-Y') . '.');
        $this->loadRoster();
    }

    public function unlockDay()
    {
        if (empty($this->roster)) {
            session()->flash('error', 'Load a roster first.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        EmployeeAttendance::where('tenant_id', $tenantId)
            ->where('date', $this->date)
            ->whereIn('employee_id', array_keys($this->roster))
            ->update(['is_locked' => false, 'locked_by' => null, 'locked_at' => null]);

        session()->flash('message', 'Attendance unlocked for ' . \Carbon\Carbon::parse($this->date)->format('d-M-Y') . '.');
        $this->loadRoster();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.lock-attendance', [
            'campuses' => Campus::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
