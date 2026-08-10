<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student\Student;
use App\Models\Finance\Challan;
use App\Models\Attendance\StudentAttendance;
use App\Models\HRM\Employee;
use App\Models\HRM\LeaveRequest;
use App\Models\HRM\SalaryPlan;
use App\Models\General\Complaint;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $currentMonth = strtolower(date('M'));
        $currentYear = date('Y');

        $totalActiveStudents = Student::where('tenant_id', $tenantId)->where('is_active', true)->count();
        $totalActiveEmployees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->count();

        $monthlyBilled = (float) Challan::where('tenant_id', $tenantId)
            ->where('month', $currentMonth)->where('year', $currentYear)
            ->sum('total_amount');
        $monthlyCollected = (float) Challan::where('tenant_id', $tenantId)
            ->where('month', $currentMonth)->where('year', $currentYear)
            ->sum('paid_amount');
        $collectionRate = $monthlyBilled > 0 ? round(($monthlyCollected / $monthlyBilled) * 100) : null;

        $todayMarked = StudentAttendance::where('tenant_id', $tenantId)->where('date', date('Y-m-d'))->count();
        $todayPresent = StudentAttendance::where('tenant_id', $tenantId)->where('date', date('Y-m-d'))->where('status', 'present')->count();
        $attendanceRate = $todayMarked > 0 ? round(($todayPresent / $todayMarked) * 100) : null;

        $pendingLeaveRequests = LeaveRequest::where('tenant_id', $tenantId)->where('status', 'pending')->count();
        $pendingSalaryApprovals = SalaryPlan::where('tenant_id', $tenantId)->where('status', 'pending')->count();
        $openComplaints = Complaint::where('tenant_id', $tenantId)->whereIn('status', ['open', 'in_progress'])->count();

        // Fee collection trend: last 6 months, oldest first.
        $collectionTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $ts = strtotime("-{$i} months");
            $m = strtolower(date('M', $ts));
            $y = date('Y', $ts);
            $collectionTrend[] = [
                'label' => date('M \'y', $ts),
                'amount' => (float) Challan::where('tenant_id', $tenantId)->where('month', $m)->where('year', $y)->sum('paid_amount'),
            ];
        }

        return view('livewire.dashboard', [
            'totalActiveStudents' => $totalActiveStudents,
            'totalActiveEmployees' => $totalActiveEmployees,
            'monthlyCollected' => $monthlyCollected,
            'collectionRate' => $collectionRate,
            'attendanceRate' => $attendanceRate,
            'todayMarked' => $todayMarked,
            'pendingLeaveRequests' => $pendingLeaveRequests,
            'pendingSalaryApprovals' => $pendingSalaryApprovals,
            'openComplaints' => $openComplaints,
            'collectionTrend' => $collectionTrend,
        ])->layout('layouts.app');
    }
}
