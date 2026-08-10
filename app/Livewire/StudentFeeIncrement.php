<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Campus\Campus;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Section;
use App\Models\Finance\FeeParticular;
use App\Models\Finance\StudentFeePlanItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentFeeIncrement extends Component
{
    // Filters
    public $campus_id = '';
    public $school_class_id = '';
    public $section_id = '';
    public $fee_particular_id = ''; // blank = all particulars

    // Increment settings
    public $increment_type  = 'percent'; // percent | fixed
    public $increment_value = '';

    public $preview = [];
    public $result_message = '';

    public function updated($property)
    {
        if (in_array($property, ['campus_id', 'school_class_id', 'section_id', 'fee_particular_id', 'increment_type', 'increment_value'])) {
            $this->buildPreview();
        }
    }

    protected function baseQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = StudentFeePlanItem::query()
            ->where('tenant_id', $tenantId)
            ->whereHas('student', function ($q) use ($tenantId) {
                $q->where('tenant_id', $tenantId)->where('is_active', true);
                if ($this->campus_id) $q->where('campus_id', $this->campus_id);
                if ($this->school_class_id) $q->where('school_class_id', $this->school_class_id);
                if ($this->section_id) $q->where('section_id', $this->section_id);
            });

        if ($this->fee_particular_id) {
            $query->where('fee_particular_id', $this->fee_particular_id);
        }

        return $query;
    }

    protected function computeNewAmount(float $current): float
    {
        $value = (float) $this->increment_value;
        if ($this->increment_type === 'percent') {
            return round($current * (1 + $value / 100), 2);
        }
        return round($current + $value, 2);
    }

    public function buildPreview()
    {
        $this->preview = [];
        $this->result_message = '';

        if (!$this->increment_value || (float) $this->increment_value <= 0) {
            return;
        }
        if (!$this->campus_id && !$this->school_class_id) {
            return; // require at least some scoping before previewing a bulk financial change
        }

        $items = $this->baseQuery()->with(['particular', 'student'])->get();

        $byStudent = $items->groupBy('student_id');
        foreach ($byStudent as $studentId => $studentItems) {
            $student = $studentItems->first()->student;
            $currentTotal = $studentItems->sum('actual_amount');
            $newTotal = $studentItems->sum(fn ($i) => $this->computeNewAmount((float) $i->actual_amount));

            $this->preview[] = [
                'student_id'   => $studentId,
                'student_name' => trim(($student->first_name ?? '') . ' ' . ($student->last_name ?? '')),
                'admission_no' => $student->admission_no ?? '',
                'particulars'  => $studentItems->count(),
                'current'      => $currentTotal,
                'new'          => $newTotal,
            ];
        }
    }

    public function apply()
    {
        if (!$this->increment_value || (float) $this->increment_value <= 0) {
            session()->flash('error', 'Enter an increment value greater than zero.');
            return;
        }
        if (!$this->campus_id && !$this->school_class_id) {
            session()->flash('error', 'Select at least a campus or class before applying a bulk fee increment.');
            return;
        }

        $items = $this->baseQuery()->get();

        if ($items->isEmpty()) {
            session()->flash('error', 'No matching fee items found for these filters.');
            return;
        }

        $affectedStudents = $items->pluck('student_id')->unique()->count();

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $item->update(['actual_amount' => $this->computeNewAmount((float) $item->actual_amount)]);
            }
        });

        $this->result_message = "Applied a " . ($this->increment_type === 'percent' ? "{$this->increment_value}%" : number_format((float) $this->increment_value, 2)) . " increment to {$items->count()} fee item(s) across {$affectedStudents} student(s).";
        session()->flash('message', $this->result_message);
        $this->preview = [];
        $this->increment_value = '';
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.student-fee-increment', [
            'campuses'          => Campus::where('tenant_id', $tenantId)->get(),
            'classes'           => SchoolClass::where('tenant_id', $tenantId)->orderBy('numeric_value')->get(),
            'sections'          => $this->school_class_id ? Section::where('tenant_id', $tenantId)->where('school_class_id', $this->school_class_id)->get() : [],
            'fee_particulars'   => FeeParticular::where('tenant_id', $tenantId)->where('is_active', true)->get(),
        ])->layout('layouts.app');
    }
}
