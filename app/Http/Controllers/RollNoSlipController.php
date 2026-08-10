<?php

namespace App\Http\Controllers;

use App\Models\Student\Student;
use App\Models\Tenant;
use Illuminate\Http\Request;

class RollNoSlipController extends Controller
{
    public function print(Request $request)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $students = Student::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->when($request->campus_id, fn ($q) => $q->where('campus_id', $request->campus_id))
            ->when($request->school_class_id, fn ($q) => $q->where('school_class_id', $request->school_class_id))
            ->when($request->section_id, fn ($q) => $q->where('section_id', $request->section_id))
            ->with(['schoolClass', 'section'])
            ->orderBy('roll_no')
            ->orderBy('first_name')
            ->get();

        $tenant = Tenant::find($tenantId);

        return view('print.roll-no-slips', compact('students', 'tenant'));
    }
}
