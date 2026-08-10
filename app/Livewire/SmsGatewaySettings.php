<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\TenantSetting;
use App\Services\SMSService;
use Illuminate\Support\Facades\Auth;

class SmsGatewaySettings extends Component
{
    public $provider = 'mock';
    public $sender_id = '';
    public $api_key = '';

    public function mount()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $config = app(SMSService::class)->gatewayConfig($tenantId);

        $this->provider = $config['provider'];
        $this->sender_id = $config['sender_id'];
        $this->api_key = $config['api_key'];
    }

    public function save()
    {
        $this->validate([
            'provider' => 'required|in:mock,twilio,local',
            'sender_id' => 'nullable|string|max:20',
            'api_key' => 'nullable|string|max:255',
        ]);

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        TenantSetting::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => 'sms_gateway_config'],
            [
                'value' => [
                    'provider' => $this->provider,
                    'sender_id' => $this->sender_id,
                    'api_key' => $this->api_key,
                ],
                'updated_by' => Auth::id(),
            ]
        );

        session()->flash('message', 'SMS Gateway settings saved successfully.');
    }

    public function render()
    {
        return view('livewire.sms-gateway-settings')->layout('layouts.app');
    }
}
