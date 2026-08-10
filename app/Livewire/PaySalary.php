<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\HRM\SalaryPayment;
use App\Models\HRM\Department;
use Illuminate\Support\Facades\Auth;

class PaySalary extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $department_id = '';
    public $month = '';
    public $year = '';
    public $status_filter = 'pending';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingDepartmentId() { $this->resetPage(); }
    public function updatingMonth() { $this->resetPage(); }
    public function updatingYear() { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = SalaryPayment::where('tenant_id', $tenantId)
            ->with(['employee.department'])
            ->when($this->search, function ($q) {
                $q->whereHas('employee', function ($eq) {
                    $eq->where('first_name', 'like', '%' . $this->search . '%')
                       ->orWhere('last_name', 'like', '%' . $this->search . '%')
                       ->orWhere('emp_no', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->department_id, function ($q) {
                $q->whereHas('employee', fn ($eq) => $eq->where('department_id', $this->department_id));
            })
            ->when($this->month, fn ($q) => $q->where('month', $this->month))
            ->when($this->year, fn ($q) => $q->where('year', $this->year));

        if ($this->status_filter !== 'all') {
            $query->where('status', $this->status_filter);
        }

        return view('livewire.pay-salary', [
            'payments' => $query->latest('id')->paginate(15),
            'departments' => Department::where('tenant_id', $tenantId)->orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    public function markPaid($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $payment = SalaryPayment::where('tenant_id', $tenantId)->findOrFail($id);

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        session()->flash('message', 'Salary payment marked as paid.');
    }
}
