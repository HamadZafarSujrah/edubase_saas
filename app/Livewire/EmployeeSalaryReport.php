<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\HRM\Employee;
use App\Models\HRM\SalaryPayment;
use Illuminate\Support\Facades\Auth;

class EmployeeSalaryReport extends Component
{
    public $employee_search = '';
    public $suggested_employees = [];
    public $employee_id = '';
    public $selected_employee = null;

    public $year;

    public $payments = [];
    public $totals = ['gross' => 0, 'deductions' => 0, 'net' => 0, 'paid_count' => 0, 'pending_count' => 0];

    public function mount()
    {
        $this->year = date('Y');
    }

    public function updatedEmployeeSearch()
    {
        if (strlen($this->employee_search) < 2) {
            $this->suggested_employees = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_employees = Employee::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->employee_search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->employee_search . '%')
                  ->orWhere('emp_no', 'like', '%' . $this->employee_search . '%');
            })
            ->limit(8)
            ->get(['id', 'first_name', 'last_name', 'emp_no'])
            ->toArray();
    }

    public function selectEmployee($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_employee = Employee::where('tenant_id', $tenantId)
            ->with(['department', 'designation'])
            ->find($id);
        $this->employee_id = $id;
        $this->suggested_employees = [];
        $this->employee_search = trim(($this->selected_employee->first_name ?? '') . ' ' . ($this->selected_employee->last_name ?? ''));
        $this->loadReport();
    }

    public function updated($property)
    {
        if ($property === 'year' && $this->employee_id) {
            $this->loadReport();
        }
    }

    public function loadReport()
    {
        if (!$this->employee_id) {
            $this->payments = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $this->payments = SalaryPayment::where('tenant_id', $tenantId)
            ->where('employee_id', $this->employee_id)
            ->where('year', $this->year)
            ->orderBy('month', 'desc')
            ->get();

        $this->totals = [
            'gross' => $this->payments->sum('gross_amount'),
            'deductions' => $this->payments->sum('deductions_amount'),
            'net' => $this->payments->sum('net_amount'),
            'paid_count' => $this->payments->where('status', 'paid')->count(),
            'pending_count' => $this->payments->where('status', 'pending')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.employee-salary-report')->layout('layouts.app');
    }
}
