<?php

namespace App\Http\Controllers;

use App\Models\HRM\SalaryPayment;
use App\Models\Tenant;
use App\Models\Finance\TenantSetting;

class PayslipController extends Controller
{
    public function print($id)
    {
        $tenantId = session('tenant_id') ?? auth()->user()->tenant_id;

        $payment = SalaryPayment::where('tenant_id', $tenantId)
            ->with(['employee.department', 'employee.designation', 'salaryPlan.allowances'])
            ->findOrFail($id);

        $tenant = Tenant::find($tenantId);

        $setting = TenantSetting::where('tenant_id', $tenantId)->where('key', 'payslip_template')->first();
        $template = [
            'show_logo' => $setting->value['show_logo'] ?? true,
            'header_color' => $setting->value['header_color'] ?? '#1e293b',
            'footer_text' => $setting->value['footer_text'] ?? '',
        ];

        return view('print.payslip', compact('payment', 'tenant', 'template'));
    }
}
