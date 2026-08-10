<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Academic\Section;
use App\Models\HRM\Employee;
use App\Models\Academic\SchoolClass;

class AssignClasses extends Component
{
    public $school_class_id = '';

    public function assignTeacher($sectionId, $employeeId)
    {
        $section = Section::findOrFail($sectionId);
        $section->update(['teacher_id' => $employeeId ?: null]);

        session()->flash('message', 'Class teacher updated for Section ' . $section->name . '.');
    }

    public function render()
    {
        // Sections aren't currently campus-scoped in practice (campus_id on
        // sections exists in the schema but nothing populates it yet -- that's
        // a gap in the Academic Setup module, not this screen), so filtering
        // is by class only, which is reliably set on every section.
        $query = Section::with(['schoolClass', 'teacher'])->where('is_active', true);

        if ($this->school_class_id) {
            $query->where('school_class_id', $this->school_class_id);
        }

        return view('livewire.assign-classes', [
            'sections' => $query->orderBy('school_class_id')->orderBy('name')->get(),
            'classes' => SchoolClass::orderBy('numeric_value')->get(),
            'teachers' => Employee::where('status', 'active')->orderBy('first_name')->get(),
        ])->layout('layouts.app');
    }
}
