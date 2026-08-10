<?php

namespace App\Http\Controllers;

use App\Models\Student\Student;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PrintFeeReminderController extends Controller
{
    public function bulkPrint(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Finance\Challan::class);

        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;
        $ids = $request->ids && is_array($request->ids) ? $request->ids : [];

        $students = Student::where('tenant_id', $tenantId)
            ->whereIn('id', $ids)
            ->with(['schoolClass', 'section', 'campus', 'challans' => fn ($q) => $q->whereIn('status', ['unpaid', 'partial', 'pending'])])
            ->get()
            ->map(function ($s) {
                $s->outstanding_total = $s->challans->sum(fn ($c) => (float) $c->total_amount - (float) $c->paid_amount - (float) $c->discount_amount);
                return $s;
            });

        $tenant = Tenant::find($tenantId);

        return view('print.fee-reminders', compact('students', 'tenant'));
    }
}
