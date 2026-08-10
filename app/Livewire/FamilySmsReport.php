<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Family;
use App\Models\Communication\SMSLog;
use Illuminate\Support\Facades\Auth;

class FamilySmsReport extends Component
{
    public $family_search = '';
    public $suggested_families = [];
    public $family_id = '';
    public $selected_family = null;

    public $logs = [];

    public function updatedFamilySearch()
    {
        if (strlen($this->family_search) < 2) {
            $this->suggested_families = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->suggested_families = Family::where('tenant_id', $tenantId)
            ->where(function ($q) {
                $q->where('family_no', 'like', '%' . $this->family_search . '%')
                  ->orWhere('father_name', 'like', '%' . $this->family_search . '%')
                  ->orWhere('guardian_phone', 'like', '%' . $this->family_search . '%');
            })
            ->limit(8)
            ->get(['id', 'family_no', 'father_name', 'guardian_phone'])
            ->toArray();
    }

    public function selectFamily($id)
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $this->selected_family = Family::where('tenant_id', $tenantId)->with('students')->find($id);
        $this->family_id = $id;
        $this->suggested_families = [];
        $this->family_search = $this->selected_family->father_name ?? ('Family #' . $this->selected_family->family_no);
        $this->loadReport();
    }

    public function loadReport()
    {
        if (!$this->family_id || !$this->selected_family) {
            $this->logs = [];
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $studentIds = $this->selected_family->students->pluck('id')->all();

        if (empty($studentIds)) {
            $this->logs = [];
            return;
        }

        $this->logs = SMSLog::where('tenant_id', $tenantId)
            ->whereIn('student_id', $studentIds)
            ->with('student')
            ->orderBy('sent_at', 'desc')
            ->limit(200)
            ->get();
    }

    public function render()
    {
        return view('livewire.family-sms-report')->layout('layouts.app');
    }
}
