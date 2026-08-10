<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\Employee;
use App\Models\HRM\Department;
use App\Models\Campus\Campus;

class EmployeeDirectory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $campus_id = '';
    public $department_id = '';
    public $status_filter = 'active';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCampusId() { $this->resetPage(); }
    public function updatingDepartmentId() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function render()
    {
        $query = Employee::with(['campus', 'department', 'designation'])
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('emp_no', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });

        if ($this->campus_id) {
            $query->where('campus_id', $this->campus_id);
        }
        if ($this->department_id) {
            $query->where('department_id', $this->department_id);
        }
        if ($this->status_filter !== 'all') {
            $query->where('status', $this->status_filter);
        }

        return view('livewire.employee-directory', [
            'employees' => $query->latest()->paginate(10),
            'campuses' => Campus::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    public function deleteEmployee($id)
    {
        Employee::findOrFail($id)->delete();
        session()->flash('message', 'Employee record deleted.');
    }
}
