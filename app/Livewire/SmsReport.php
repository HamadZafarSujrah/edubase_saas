<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Communication\SMSLog;
use Illuminate\Support\Facades\Auth;

class SmsReport extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $start_date;
    public $end_date;
    public $type = '';
    public $status = '';

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');
    }

    public function updatingStartDate() { $this->resetPage(); }
    public function updatingEndDate() { $this->resetPage(); }
    public function updatingType() { $this->resetPage(); }
    public function updatingStatus() { $this->resetPage(); }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = SMSLog::where('tenant_id', $tenantId)
            ->whereDate('sent_at', '>=', $this->start_date)
            ->whereDate('sent_at', '<=', $this->end_date)
            ->with('student')
            ->orderBy('sent_at', 'desc');

        if ($this->type) {
            $query->where('type', $this->type);
        }
        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.sms-report', [
            'logs' => $query->paginate(20),
        ])->layout('layouts.app');
    }
}
