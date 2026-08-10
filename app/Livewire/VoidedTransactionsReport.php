<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\ChallanVoidLog;
use Illuminate\Support\Facades\Auth;

class VoidedTransactionsReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $from_date = '';
    public $to_date = '';

    public function updated() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = ChallanVoidLog::with('voidedBy')
            ->where('tenant_id', $tenantId);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('student_name', 'like', '%' . $this->search . '%')
                  ->orWhere('challan_no', 'like', '%' . $this->search . '%');
            });
        }
        if ($this->from_date) {
            $query->whereDate('created_at', '>=', $this->from_date);
        }
        if ($this->to_date) {
            $query->whereDate('created_at', '<=', $this->to_date);
        }

        $totalVoided = (clone $query)->sum('voided_amount');
        $logs = $query->latest()->paginate(15);

        return view('livewire.voided-transactions-report', [
            'logs' => $logs,
            'totalVoided' => $totalVoided,
        ])->layout('layouts.app');
    }
}
