<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use Illuminate\Support\Facades\Auth;

class PrintFeeReminder extends Component
{
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    public $status_filter = 'active';
    public $min_outstanding = '';

    public $selected_students = [];
    public $selectAll = false;

    public function updated($property)
    {
        if (in_array($property, ['campus_id', 'school_class_id', 'section_id', 'status_filter', 'min_outstanding'])) {
            $this->selected_students = [];
            $this->selectAll = false;
        }
    }

    protected function getDefaultersQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = Student::where('tenant_id', $tenantId)
            ->whereHas('challans', fn ($q) => $q->whereIn('status', ['unpaid', 'partial', 'pending']));

        if ($this->campus_id) $query->where('campus_id', $this->campus_id);
        if ($this->school_class_id) $query->where('school_class_id', $this->school_class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);
        if ($this->status_filter !== 'all') $query->where('is_active', $this->status_filter === 'active');

        return $query->with(['schoolClass', 'section', 'challans' => fn ($q) => $q->whereIn('status', ['unpaid', 'partial', 'pending'])]);
    }

    public function getDefaultersProperty()
    {
        $students = $this->getDefaultersQuery()->get()->map(function ($s) {
            $s->outstanding_total = $s->challans->sum(fn ($c) => (float) $c->total_amount - (float) $c->paid_amount - (float) $c->discount_amount);
            return $s;
        });

        if ($this->min_outstanding !== '') {
            $students = $students->filter(fn ($s) => $s->outstanding_total >= (float) $this->min_outstanding);
        }

        return $students->sortByDesc('outstanding_total')->values();
    }

    public function updatedSelectAll($value)
    {
        $this->selected_students = $value
            ? $this->defaulters->pluck('id')->map(fn ($id) => (string) $id)->toArray()
            : [];
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.print-fee-reminder', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
            'classes'  => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections' => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
        ])->layout('layouts.app');
    }
}
