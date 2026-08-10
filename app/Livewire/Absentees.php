<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Attendance\StudentAttendance;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use Illuminate\Support\Facades\Auth;

class Absentees extends Component
{
    public $date;
    public $campus_id = '';
    public $school_class_id = '';

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function getAbsenteesProperty()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = StudentAttendance::where('tenant_id', $tenantId)
            ->where('date', $this->date)
            ->whereIn('status', ['absent', 'leave'])
            ->with(['student.schoolClass', 'student.section', 'student.campus']);

        if ($this->campus_id) {
            $query->whereHas('student', fn ($q) => $q->where('campus_id', $this->campus_id));
        }
        if ($this->school_class_id) {
            $query->whereHas('student', fn ($q) => $q->where('school_class_id', $this->school_class_id));
        }

        return $query->get()->sortBy(fn ($a) => $a->student->first_name ?? '');
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.absentees', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
        ])->layout('layouts.app');
    }
}
