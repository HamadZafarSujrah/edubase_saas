<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student\Student;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;

class StudentFeePlanList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $class_id = '';
    public $section_id = '';

    public function updatingSearch() { $this->resetPage(); }
    public function updatingClassId() { $this->resetPage(); }

    public function render()
    {
        $query = Student::with(['campus', 'schoolClass', 'section', 'feePlan'])
            ->where(function($q) {
                $q->where('first_name', 'like', '%'.$this->search.'%')
                  ->orWhere('last_name', 'like', '%'.$this->search.'%')
                  ->orWhere('admission_no', 'like', '%'.$this->search.'%')
                  ->orWhere('father_name', 'like', '%'.$this->search.'%');
            });

        if ($this->class_id) $query->where('school_class_id', $this->class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);

        return view('livewire.student-fee-plan-list', [
            'students' => $query->latest()->paginate(15),
            'classes' => SchoolClass::all(),
            'sections' => $this->class_id ? Section::where('school_class_id', $this->class_id)->get() : []
        ])->layout('layouts.app');
    }
}
