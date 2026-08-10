<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\TenantSetting;
use Illuminate\Support\Facades\Auth;

class PayslipTemplateManager extends Component
{
    public $show_logo = true;
    public $header_color = '#1e293b';
    public $footer_text = '';

    public function mount()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $setting = TenantSetting::where('tenant_id', $tenantId)->where('key', 'payslip_template')->first();

        if ($setting && is_array($setting->value)) {
            $this->show_logo = $setting->value['show_logo'] ?? true;
            $this->header_color = $setting->value['header_color'] ?? '#1e293b';
            $this->footer_text = $setting->value['footer_text'] ?? '';
        }
    }

    public function save()
    {
        $this->validate([
            'header_color' => 'required|string|max:20',
            'footer_text' => 'nullable|string|max:255',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        TenantSetting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => 'payslip_template'],
            [
                'value' => [
                    'show_logo' => (bool) $this->show_logo,
                    'header_color' => $this->header_color,
                    'footer_text' => $this->footer_text,
                ],
                'updated_by' => Auth::id(),
            ]
        );

        session()->flash('message', 'Payslip template saved successfully.');
    }

    public function render()
    {
        return view('livewire.payslip-template-manager')->layout('layouts.app');
    }
}
