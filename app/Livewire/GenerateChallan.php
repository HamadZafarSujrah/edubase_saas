<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use App\Models\Student\Student;
use App\Models\Finance\FeePlanParticular;
use App\Models\Finance\Challan;
use App\Models\Finance\ChallanItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\FeeService;

class GenerateChallan extends Component
{

    // Injecting FeeService using Livewire's app() or dependency injection

    public $campus_id, $school_class_id, $section_id, $month, $year, $due_date;
    public $session_id; // Added for session filtering
    public $processing = false;
    public $generated_count = 0;
    
    public $selected_students = [];
    public $selectAll = false;
    public $showOnlyPending = true; // Default to showing only students without challans

    protected $queryString = ['campus_id', 'school_class_id', 'section_id'];

    public function getFilteredStudentsProperty()
    {
        $query = Student::with(['campus', 'schoolClass', 'section', 'session']);
        
        if ($this->campus_id) $query->where('campus_id', $this->campus_id);
        if ($this->school_class_id) $query->where('school_class_id', $this->school_class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);
        if ($this->session_id) $query->where('session_id', $this->session_id);

        if ($this->showOnlyPending) {
            $query->whereDoesntHave('challans', function($q) {
                $q->where('month', $this->month)
                  ->where('year', $this->year);
            });
        }

        return $query->whereNotNull('fee_plan_id')->has('feePlanItems')->get();
    }

    public function mount()
    {
        $this->month = strtolower(date('M'));
        $this->year = date('Y');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));

        $activeSession = \App\Models\Academic\Session::where('is_active', true)->first();
        if ($activeSession) {
            $this->session_id = $activeSession->id;
        }
    }

    public function updatedMonth() { $this->resetSelection(); }
    public function updatedYear() { $this->resetSelection(); }
    public function updatedCampusId() { $this->resetSelection(); }
    public function updatedSchoolClassId() { $this->resetSelection(); }
    public function updatedSectionId() { $this->resetSelection(); }
    public function updatedSessionId() { $this->resetSelection(); }

    protected function resetSelection()
    {
        $this->selected_students = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_students = $this->filteredStudents->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected_students = [];
        }
    }

    public function generate()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('fee.challan.generate')) {
            session()->flash('error', 'You are not authorized to generate challans.');
            return;
        }

        $this->validate([
            'month' => 'required',
            'year' => 'required',
            'due_date' => 'required|date'
        ]);

        $this->processing = true;
        $this->generated_count = 0;

        // 1. Determine target students
        if (!empty($this->selected_students)) {
            $students = Student::whereIn('id', $this->selected_students)->get();
        } else {
            $students = $this->filteredStudents;
        }

        if ($students->isEmpty()) {
            $this->processing = false;
            session()->flash('error', "No students selected or found matching filters.");
            $this->dispatch('challans-error', message: "No students selected or found matching filters.");
            return;
        }

        $feeService = app(FeeService::class);

        foreach ($students as $student) {
            $challan = $feeService->generateMonthlyChallan($student, $this->month, $this->year, $this->due_date);
            
            if ($challan) {
                $this->generated_count++;
            }
        }


        $this->selected_students = [];
        $this->selectAll = false;
        $this->processing = false;
        $msg = "Successfully generated {$this->generated_count} challans for " . strtoupper($this->month) . " {$this->year}.";
        session()->flash('message', $msg);
        $this->dispatch('challans-generated', message: $msg);
    }

    public function render()
    {
        return view('livewire.generate-challan', [
            'campuses' => Campus::all(),
            'classes' => \App\Models\Academic\SchoolClass::all(),
            'sections' => $this->school_class_id ? \App\Models\Academic\Section::where('school_class_id', $this->school_class_id)->get() : [],
            'months' => ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']
        ])->layout('layouts.app');
    }
}
