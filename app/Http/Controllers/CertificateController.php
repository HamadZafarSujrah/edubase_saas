<?php

namespace App\Http\Controllers;

use App\Models\Student\Certificate;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function print($id)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $certificate = Certificate::where('tenant_id', $tenantId)
            ->with(['student.schoolClass', 'student.campus', 'issuedBy'])
            ->findOrFail($id);

        $this->authorize('view', $certificate->student);

        $tenant = Tenant::find($tenantId);

        return view('print.certificate', compact('certificate', 'tenant'));
    }

    public function downloadPdf($id)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $certificate = Certificate::where('tenant_id', $tenantId)
            ->with(['student.schoolClass', 'student.campus', 'issuedBy'])
            ->findOrFail($id);

        $this->authorize('view', $certificate->student);

        $tenant = Tenant::find($tenantId);

        $pdf = Pdf::loadView('print.certificate', compact('certificate', 'tenant'))->setPaper('a4', 'portrait');

        return $pdf->download('certificate-' . $certificate->student->admission_no . '.pdf');
    }
}
