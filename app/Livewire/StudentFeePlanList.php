<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student\Student;
use App\Models\Finance\FeePlan;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;

class StudentFeePlanList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search       = '';
    public $class_id     = '';
    public $section_id   = '';
    public $fee_plan_search = '';
    public $current_tenant_id;
    public $mode = 'create';

    public function mount() {
        $this->current_tenant_id = session('tenant_id') ?? auth()->user()->tenant_id;
        if (request()->routeIs('finance.view-edit-fee-plans')) {
            $this->mode = 'edit';
        }
    }

    public function updatingSearch()    { $this->resetPage(); }
    public function updatingClassId()   { $this->resetPage(); }
    public function updatingSectionId() { $this->resetPage(); }

    public function deleteFeePlan($studentId)
    {
        $student = Student::findOrFail($studentId);
        if ($student->tenant_id != $this->current_tenant_id) abort(403);

        \Illuminate\Support\Facades\DB::transaction(function() use ($student) {
            \App\Models\Finance\StudentFeePlanItem::where('student_id', $student->id)->delete();
            $student->update(['fee_plan_id' => null]);
        });
        
        session()->flash('message', 'Student fee plan was successfully deleted.');
    }

    public function render()
    {
        $tenantId = $this->current_tenant_id;

        $query = Student::with(['campus', 'schoolClass', 'section', 'feePlan', 'feePlanItems.particular'])
            ->where('tenant_id', $tenantId);
            
        if ($this->mode === 'create') {
            $query->doesntHave('feePlanItems');
        } else {
            $query->has('feePlanItems');
        }

        $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%')
                  ->orWhere('father_name', 'like', '%' . $this->search . '%');
            });

        if ($this->class_id)   $query->where('school_class_id', $this->class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);

        if ($this->fee_plan_search) {
            $query->whereHas('feePlan', function ($q) {
                $q->where('name', 'like', '%' . $this->fee_plan_search . '%');
            });
        }

        return view('livewire.student-fee-plan-list', [
            'students'  => $query->latest()->paginate(50),
            'classes'   => SchoolClass::where('tenant_id', $tenantId)->orderBy('name')->get(),
            'sections'  => $this->class_id
                            ? Section::where('school_class_id', $this->class_id)->get()
                            : [],
            'fee_plans' => FeePlan::where('tenant_id', $tenantId)->where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
