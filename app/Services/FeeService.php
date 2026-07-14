<?php

namespace App\Services;

use App\Models\Finance\Challan;
use App\Models\Finance\ChallanItem;
use App\Models\Student\Student;
use App\Models\Finance\StudentFeePlanItem;
use Illuminate\Support\Facades\DB;

class FeeService
{
    /**
     * Generates a monthly fee challan for a given student based on their personalized fee plan.
     *
     * @param Student $student
     * @param string $month
     * @param string|int $year
     * @param string $dueDate
     * @return Challan|null
     * @throws \Exception
     */
    public function generateMonthlyChallan(Student $student, $month, $year, $dueDate)
    {
        // Check if challan already exists for this month/year/student
        $exists = Challan::where([
            'student_id' => $student->id, 
            'month' => $month, 
            'year' => $year
        ])->exists();
        
        if ($exists) return null;

        // Get INDIVIDUALIZED items for this student
        $personalParticulars = StudentFeePlanItem::with('particular')
            ->where('student_id', $student->id)
            ->get();

        if ($personalParticulars->isEmpty()) return null;

        DB::beginTransaction();
        try {
            // Calculate net total (Actual - Discount)
            $total = 0;
            foreach ($personalParticulars as $pp) {
                $total += ($pp->actual_amount - $pp->discount_amount);
            }
            
            // Create Challan Header
            $challan = Challan::create([
                'tenant_id' => $student->tenant_id,
                'student_id' => $student->id,
                'challan_no' => 'CHL-' . strtoupper($month) . '-' . $year . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT) . '-' . rand(10,99),
                'month' => $month,
                'year' => $year,
                'issue_date' => date('Y-m-d'),
                'due_date' => $dueDate,
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
            
            DB::commit();
            return $challan;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
