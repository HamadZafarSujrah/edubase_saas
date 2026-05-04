<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Finance\StudentFeePlanItem;

class StudentProfile extends Component
{
    public $student_id;
    public $student;
    public $fee_items;
    public $tenant;

    public function mount($id)
    {
        $this->student_id = $id;
        $this->student = Student::with(['campus', 'schoolClass', 'section', 'session'])->findOrFail($id);
        
        $tenantId = session('tenant_id') ?? 1;
        $this->tenant = \App\Models\Tenant::find($tenantId);

        $this->fee_items = StudentFeePlanItem::where('student_id', $id)
            ->with('particular')
            ->get();
    }

    public function render()
    {
        return view('livewire.student-profile')->layout('layouts.app');
    }
}
