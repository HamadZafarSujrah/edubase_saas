<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use App\Models\Student\Student;
use App\Models\Finance\FeePlanParticular;
use App\Models\Finance\Challan;
use App\Models\Finance\ChallanItem;
use Carbon\Carbon;
use DB;

class GenerateChallan extends Component
{
    public $campus_id, $school_class_id, $section_id, $month, $year, $due_date;
    public $processing = false;
    public $generated_count = 0;

    protected $queryString = ['campus_id', 'school_class_id', 'section_id'];

    public function getFilteredStudentsProperty()
    {
        $query = Student::with(['campus', 'schoolClass', 'section']);
        
        if ($this->campus_id) $query->where('campus_id', $this->campus_id);
        if ($this->school_class_id) $query->where('school_class_id', $this->school_class_id);
        if ($this->section_id) $query->where('section_id', $this->section_id);

        return $query->get();
    }

    public function mount()
    {
        $this->month = strtolower(date('M'));
        $this->year = date('Y');
        $this->due_date = date('Y-m-d', strtotime('+10 days'));
    }

    public function generate()
    {
        $this->validate([
            'month' => 'required',
            'year' => 'required',
            'due_date' => 'required|date'
        ]);

        $this->processing = true;
        $this->generated_count = 0;

        // 1. Get students for this session/campus who have a Fee Plan
        $query = Student::whereNotNull('fee_plan_id');
        if ($this->campus_id) {
            $query->where('campus_id', $this->campus_id);
        }

        $students = $query->get();

        foreach ($students as $student) {
            // Check if challan already exists for this month/year/student
            $exists = Challan::where(['student_id' => $student->id, 'month' => $this->month, 'year' => $this->year])->exists();
            if ($exists) continue;

            // NEW: Get INDIVIDUALIZED items for this student and this month
            $personalParticulars = \App\Models\Finance\StudentFeePlanItem::with('particular')
                ->where([
                    'student_id' => $student->id,
                    $this->month => true
                ])->get();

            if ($personalParticulars->isEmpty()) continue;

            DB::transaction(function() use ($student, $personalParticulars) {
                // Calculate net total (Actual - Discount)
                $total = 0;
                foreach ($personalParticulars as $pp) {
                    $total += ($pp->actual_amount - $pp->discount_amount);
                }
                
                // Create Challan Header
                $challan = Challan::create([
                    'tenant_id' => $student->tenant_id,
                    'student_id' => $student->id,
                    'challan_no' => 'CHL-' . strtoupper($this->month) . '-' . $this->year . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT) . '-' . rand(10,99),
                    'month' => $this->month,
                    'year' => $this->year,
                    'issue_date' => date('Y-m-d'),
                    'due_date' => $this->due_date,
                    'total_amount' => $total,
                    'status' => 'pending'
                ]);

                // Create Items using customized amounts
                foreach ($personalParticulars as $pp) {
                    $netAmount = $pp->actual_amount - $pp->discount_amount;
                    if ($netAmount <= 0) continue; // Skip if fully discounted

                    ChallanItem::create([
                        'challan_id' => $challan->id,
                        'fee_particular_id' => $pp->fee_particular_id,
                        'particular_name' => $pp->particular->name,
                        'amount' => $netAmount
                    ]);
                }
            });

            $this->generated_count++;
        }

        $this->processing = false;
        session()->flash('message', "Successfully generated {$this->generated_count} challans for " . strtoupper($this->month) . " {$this->year}.");
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
