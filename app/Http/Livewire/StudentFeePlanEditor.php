<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Finance\StudentFeePlanItem;
use Carbon\Carbon;

class StudentFeePlanEditor extends Component
{
    public $student_id;
    public $effect_from = '';
    public $items = [];
    public $available_months = [];
    public $new_fee_plan_id; // To handle switching

    public function updatedNewFeePlanId($value)
    {
        if (!$value) return;

        $student = Student::find($this->student_id);
        $student->update(['fee_plan_id' => $value]);

        // Clear existing customized items
        StudentFeePlanItem::where('student_id', $this->student_id)->delete();

        // Clone from Master Plan
        $masterParticulars = \App\Models\Finance\FeePlanParticular::where('fee_plan_id', $value)->get();
        $admissionMonth = strtolower(date('M', strtotime($student->admission_date)));

        foreach ($masterParticulars as $mp) {
            $data = [
                'tenant_id' => $student->tenant_id,
                'student_id' => $student->id,
                'fee_particular_id' => $mp->fee_particular_id,
                'actual_amount' => $mp->amount,
                'jan' => $mp->jan, 'feb' => $mp->feb, 'mar' => $mp->mar, 'apr' => $mp->apr,
                'may' => $mp->may, 'jun' => $mp->jun, 'jul' => $mp->jul, 'aug' => $mp->aug,
                'sep' => $mp->sep, 'oct' => $mp->oct, 'nov' => $mp->nov, 'dec' => $mp->dec,
            ];

            // If it is a first-time fee, auto-check the admission month
            if ($mp->is_first_time) {
                $data[$admissionMonth] = true;
            }

            StudentFeePlanItem::create($data);
        }

        $this->loadItems(); // Reload UI
        session()->flash('message', 'Master Fee Plan reassigned successfully!');
    }

    public function mount($id)
    {
        $this->student_id = $id;
        $this->loadItems();
        $this->generateAvailableMonths();
    }

    public function generateAvailableMonths()
    {
        $months = [];
        $start = Carbon::now()->subYear()->startOfYear();
        $end = Carbon::now()->addYear()->endOfYear();

        while ($start <= $end) {
            $months[] = [
                'val' => strtolower($start->format('M')),
                'label' => $start->format('F Y')
            ];
            $start->addMonth();
        }
        $this->available_months = $months;
    }

    public function updatedEffectFrom($value)
    {
        if (!$value) return;

        $months = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'];
        $startIndex = array_search($value, $months);

        foreach ($this->items as $id => $item) {
            foreach ($months as $index => $month) {
                // If current month is before the 'Effect From' month, turn it OFF
                $this->items[$id][$month] = ($index >= $startIndex);
            }
        }
    }

    public function loadItems()
    {
        $this->items = []; // Clear current list to prevent duplicates
        $data = StudentFeePlanItem::with('particular')
            ->where('student_id', $this->student_id)
            ->get();
        
        foreach ($data as $item) {
            $particularName = $item->particular->name ?? 'Unknown';
            $isFirstTime = false;
            
            // Check database flag
            if ($item->particular && $item->particular->is_first_time) {
                $isFirstTime = true;
            }

            // Fallback: Check if name contains "Admission", "Registration", or "Prospectus"
            $lowerName = strtolower($particularName);
            if (str_contains($lowerName, 'admission') || str_contains($lowerName, 'registration') || str_contains($lowerName, 'prospectus')) {
                $isFirstTime = true;
            }

            $this->items[$item->id] = [
                'particular_name' => $particularName,
                'is_first_time' => $isFirstTime,
                'actual_fee' => $item->actual_amount,
                'discount' => $item->discount_amount,
                'jan' => $item->jan, 'feb' => $item->feb, 'mar' => $item->mar, 'apr' => $item->apr,
                'may' => $item->may, 'jun' => $item->jun, 'jul' => $item->jul, 'aug' => $item->aug,
                'sep' => $item->sep, 'oct' => $item->oct, 'nov' => $item->nov, 'dec' => $item->dec,
            ];
        }
    }

    public function save()
    {
        foreach ($this->items as $id => $data) {
            StudentFeePlanItem::find($id)->update([
                'actual_amount' => $data['actual_fee'],
                'discount_amount' => $data['discount'],
                'jan' => $data['jan'], 'feb' => $data['feb'], 'mar' => $data['mar'], 'apr' => $data['apr'],
                'may' => $data['may'], 'jun' => $data['jun'], 'jul' => $data['jul'], 'aug' => $data['aug'],
                'sep' => $data['sep'], 'oct' => $data['oct'], 'nov' => $data['nov'], 'dec' => $data['dec']
            ]);
        }
        session()->flash('message', 'Student Fee Plan updated successfully!');
        return redirect()->route('view-edit-fee-plans');
    }

    public function render()
    {
        return view('livewire.student-fee-plan-editor', [
            'student' => Student::with(['campus', 'schoolClass', 'section', 'feePlan'])->findOrFail($this->student_id),
            'fee_plans' => \App\Models\Finance\FeePlan::all()
        ])->layout('layouts.app');
    }
}
