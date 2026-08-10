<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\CampusClass;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsExport;

class StudentDirectory extends Component
{
    use WithPagination, AuthorizesRequests;
    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    // 'active' (default) | 'inactive' | 'all' -- lets alumni/non-enrolled
    // students be found deliberately instead of always being mixed in with
    // (or, with no filter at all, indistinguishable from) currently
    // enrolled students.
    public $status_filter = 'active';

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
    public function updatingStatusFilter() { $this->resetPage(); }

    public function render()
    {
        $query = Student::with(['campus', 'schoolClass', 'section', 'media'])
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

        if ($this->status_filter !== 'all') {
            $query->where('is_active', $this->status_filter === 'active');
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
        $student = Student::findOrFail($id);
        $this->authorize('delete', $student);

        $hasPendingChallans = \App\Models\Finance\Challan::where('student_id', $id)
            ->where('status', '!=', 'paid')
            ->exists();

        if ($hasPendingChallans) {
            session()->flash('error', 'Cannot delete student: There are unpaid/pending challans. Please either pay them or void them from the Pay/Print page first.');
            return;
        }

        if ($student->feePlanItems()->exists()) {
            session()->flash('error', 'Cannot delete student: A fee plan has been actively created and customized for this student. Please delete the fee plan from the "View / Edit Fee Plans" page first.');
            return;
        }

        $student->delete();
        session()->flash('message', 'Student record deleted.');
    }

    public function exportExcel()
    {
        $tenant_id = session('tenant_id') ?? auth()->user()->tenant_id;
        return Excel::download(new StudentsExport($tenant_id), 'students_list.xlsx');
    }
}
