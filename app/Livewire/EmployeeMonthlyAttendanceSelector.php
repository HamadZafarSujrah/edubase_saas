<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Department;
use Illuminate\Support\Facades\Auth;

class EmployeeMonthlyAttendanceSelector extends Component
{
    public $department_id = '';
    public $month;
    public $year;

    public function mount()
    {
        $this->month = date('m');
        $this->year = date('Y');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.employee-monthly-attendance-selector', [
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
