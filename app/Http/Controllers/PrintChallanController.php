<?php

namespace App\Http\Controllers;

use App\Models\Finance\Challan;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintChallanController extends Controller
{
    public function bulkPrint(Request $request)
    {
        $this->authorize('viewAny', Challan::class);

        $query = Challan::with(['student.schoolClass', 'student.section', 'student.campus', 'items']);

        // 1. Filter by specific IDs (if provided)
        if ($request->ids && is_array($request->ids)) {
            $query->whereIn('id', $request->ids);
        } else {
            // 2. Otherwise, filter based on parameters passed from the billing dashboard
            if ($request->month) $query->where('month', $request->month);
            if ($request->year) $query->where('year', $request->year);
            
            if ($request->class_id || $request->section_id || $request->campus_id) {
                $query->whereHas('student', function($q) use ($request) {
                    if ($request->campus_id) $q->where('campus_id', $request->campus_id);
                    if ($request->class_id) $q->where('school_class_id', $request->class_id);
                    if ($request->section_id) $q->where('section_id', $request->section_id);
                });
            }

            // Handle individual search if provided
            if ($request->search) {
                $query->whereHas('student', function($q) use ($request) {
                    $q->where('admission_no', $request->search)
                      ->orWhere('first_name', 'like', "%{$request->search}%");
                });
            }
        }

        $challans = $query->get();
        
        // Get school info from tenant
        $tenant = Tenant::find(session('tenant_id'));

        return view('print.challans-3-part', compact('challans', 'tenant'));
    }

    public function downloadPdf(Request $request)
    {
        $this->authorize('viewAny', Challan::class);

        $query = Challan::with(['student.schoolClass', 'student.section', 'student.campus', 'items']);
        
        if ($request->ids && is_array($request->ids)) {
            $query->whereIn('id', $request->ids);
        }

        $challans = $query->get();
        $tenant = Tenant::find(session('tenant_id'));

        $pdf = Pdf::loadView('print.challans-3-part', compact('challans', 'tenant'))
                  ->setPaper('a4', 'landscape');

        return $pdf->download('fee-challans.pdf');
    }
}
