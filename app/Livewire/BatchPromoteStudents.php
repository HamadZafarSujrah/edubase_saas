<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Academic\Session;
use Illuminate\Support\Facades\Auth;

class BatchPromoteStudents extends Component
{
    // 'promote' moves selected students to a new class (+ new session);
    // 'transfer' moves them to a different section within the SAME class.
    public $mode = 'promote';

    // Source filters
    public $campus_id;
    public $school_class_id;
    public $section_id = '';
    public $session_id = '';

    // Selection
    public $selected_students = [];
    public $selectAll = false;

    // Targets
    public $target_class_id = '';
    public $target_session_id = '';
    public $target_section_id = '';

    public $result_message = '';

    public function mount()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $activeSession = Session::where('tenant_id', $tenantId)->where('is_active', true)->first();
        $this->session_id = $activeSession?->id ?? '';
    }

    public function updatedMode()
    {
        $this->resetTargets();
    }

    public function updatedCampusId() { $this->resetSelection(); }
    public function updatedSchoolClassId()
    {
        $this->resetSelection();
        $this->suggestNextClass();
    }
    public function updatedSectionId() { $this->resetSelection(); }
    public function updatedSessionId() { $this->resetSelection(); }

    protected function resetSelection()
    {
        $this->selected_students = [];
        $this->selectAll = false;
        $this->result_message = '';
    }

    protected function resetTargets()
    {
        $this->target_class_id = '';
        $this->target_session_id = '';
        $this->target_section_id = '';
    }

    protected function suggestNextClass()
    {
        if ($this->mode !== 'promote' || !$this->school_class_id) {
            return;
        }
        $current = SchoolClass::find($this->school_class_id);
        if (!$current) {
            return;
        }
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $next = SchoolClass::where('tenant_id', $tenantId)
            ->where('numeric_value', '>', $current->numeric_value)
            ->orderBy('numeric_value')
            ->first();
        $this->target_class_id = $next?->id ?? '';
    }

    public function updatedSelectAll($value)
    {
        $this->selected_students = $value
            ? $this->getFilteredStudents()->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    protected function getFilteredStudents()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Student::where('tenant_id', $tenantId)->where('is_active', true);

        if ($this->campus_id) $query->where('campus_id', $this->campus_id);
        if ($this->school_class_id) $query->where('school_class_id', $this->school_class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);
        if ($this->session_id) $query->where('session_id', $this->session_id);

        return $query->with(['schoolClass', 'section'])->orderBy('first_name')->get();
    }

    public function apply()
    {
        if (empty($this->selected_students)) {
            session()->flash('error', 'Select at least one student.');
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        if ($this->mode === 'promote') {
            if (!$this->target_class_id || !$this->target_session_id) {
                session()->flash('error', 'Select a target class and target session to promote into.');
                return;
            }

            $updated = Student::where('tenant_id', $tenantId)
                ->whereIn('id', $this->selected_students)
                ->update([
                    'school_class_id' => $this->target_class_id,
                    'session_id'      => $this->target_session_id,
                    'section_id'      => $this->target_section_id ?: null,
                ]);

            $targetClass = SchoolClass::find($this->target_class_id);
            $this->result_message = "Promoted {$updated} student(s) to {$targetClass?->name}. Remember: fee plans are not carried over automatically -- assign/generate fee plans for the new class separately.";
        } else {
            if (!$this->target_section_id) {
                session()->flash('error', 'Select a target section to transfer into.');
                return;
            }

            $updated = Student::where('tenant_id', $tenantId)
                ->whereIn('id', $this->selected_students)
                ->update(['section_id' => $this->target_section_id]);

            $targetSection = Section::find($this->target_section_id);
            $this->result_message = "Transferred {$updated} student(s) to section {$targetSection?->name}.";
        }

        session()->flash('message', $this->result_message);
        $this->resetSelection();
        $this->resetTargets();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $targetSections = [];
        if ($this->mode === 'promote' && $this->target_class_id) {
            $targetSections = Section::where('tenant_id', $tenantId)->where('school_class_id', $this->target_class_id)->get();
        } elseif ($this->mode === 'transfer' && $this->school_class_id) {
            $targetSections = Section::where('tenant_id', $tenantId)
                ->where('school_class_id', $this->school_class_id)
                ->when($this->section_id, fn ($q) => $q->where('id', '!=', $this->section_id))
                ->get();
        }

        return view('livewire.batch-promote-students', [
            'campuses'        => Campus::where('tenant_id', $tenantId)->get(),
            'classes'         => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections'        => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
            'sessions'        => Session::where('tenant_id', $tenantId)->orderBy('start_date', 'desc')->get(),
            'targetSections'  => $targetSections,
            'students'        => $this->getFilteredStudents(),
        ])->layout('layouts.app');
    }
}
