<?php

namespace App\Http\Controllers;

use App\Models\Finance\Challan;
use Illuminate\Http\Request;
use App\Models\Tenant\Tenant;

class PrintChallanController extends Controller
{
    public function bulkPrint(Request $request)
    {
        $query = Challan::with(['student.schoolClass', 'student.section', 'student.campus', 'items']);

        // Filter based on parameters passed from the billing dashboard
        if ($request->month) $query->where('month', $request->month);
        if ($request->year) $query->where('year', $request->year);
        
        if ($request->class_id || $request->section_id || $request->campus_id) {
            $query->whereHas('student', function($q) use ($request) {
                if ($request->campus_id) $q->where('campus_id', $request->campus_id);
                if ($request->class_id) $q->where('school_class_id', $request->class_id);
                if ($request->section_id) $q->where('section_id', $request->section_id);
            });
        }

        $challans = $query->get();
        
        // Get school info from tenant
        $tenant = Tenant::find(session('tenant_id'));

        return view('print.challans-3-part', compact('challans', 'tenant'));
    }
}
