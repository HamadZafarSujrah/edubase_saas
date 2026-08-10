<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\TenantSetting;
use Illuminate\Support\Facades\Auth;

class WhatsAppSettings extends Component
{
    public $is_enabled = false;
    public $business_number = '';
    public $api_key = '';

    public function mount()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $setting = TenantSetting::where('tenant_id', $tenantId)->where('key', 'whatsapp_config')->first();

        if ($setting && is_array($setting->value)) {
            $this->is_enabled = $setting->value['is_enabled'] ?? false;
            $this->business_number = $setting->value['business_number'] ?? '';
            $this->api_key = $setting->value['api_key'] ?? '';
        }
    }

    public function save()
    {
        $this->validate([
            'business_number' => 'nullable|string|max:20',
            'api_key' => 'nullable|string|max:255',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        TenantSetting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => 'whatsapp_config'],
            [
                'value' => [
                    'is_enabled' => (bool) $this->is_enabled,
                    'business_number' => $this->business_number,
                    'api_key' => $this->api_key,
                ],
                'updated_by' => Auth::id(),
            ]
        );

        session()->flash('message', 'WhatsApp settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.whatsapp-settings')->layout('layouts.app');
    }
}
