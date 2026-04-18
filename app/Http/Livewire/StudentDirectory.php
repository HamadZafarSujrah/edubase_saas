<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\CampusClass;

class StudentDirectory extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';

    // Advanced Column Visibility (as per your suggestion)
    public $showColumns = [
        'photo' => true,
        'admission_no' => true,
        'name' => true,
        'academic' => true,
        'family' => true,
        'cnic' => false,
        'dob' => false,
        'gender' => false,
        'religion' => false,
        'contact' => true,
        'address' => false,
        'guardian' => false,
        'logistics' => false,
        'status' => true,
    ];

    public function toggleColumn($column)
    {
        if (isset($this->showColumns[$column])) {
            $this->showColumns[$column] = !$this->showColumns[$column];
        }
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingCampusId() { $this->resetPage(); $this->school_class_id = ''; $this->section_id = ''; }
    public function updatingSchoolClassId() { $this->resetPage(); $this->section_id = ''; }

    public function render()
    {
        $query = Student::with(['campus', 'schoolClass', 'section'])
            ->where(function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%')
                  ->orWhere('father_name', 'like', '%' . $this->search . '%');
            });

        if ($this->campus_id) {
            $query->where('campus_id', $this->campus_id);
        }

        if ($this->school_class_id) {
            $query->where('school_class_id', $this->school_class_id);
        }

        if ($this->section_id) {
            $query->where('section_id', $this->section_id);
        }

        // Logic for dynamic dropdowns
        $availableClasses = [];
        if ($this->campus_id) {
            $classIds = CampusClass::where('campus_id', $this->campus_id)->pluck('school_class_id');
            $availableClasses = SchoolClass::whereIn('id', $classIds)->get();
        }

        $availableSections = [];
        if ($this->school_class_id) {
            $availableSections = Section::where('school_class_id', $this->school_class_id)->get();
        }

        return view('livewire.student-directory', [
            'students' => $query->latest()->paginate(10),
            'campuses' => Campus::all(),
            'classes' => $availableClasses,
            'sections' => $availableSections
        ])->layout('layouts.app');
    }

    public function deleteStudent($id)
    {
        Student::findOrFail($id)->delete();
        session()->flash('message', 'Student record deleted.');
    }
}
