<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;

class SelectTenant extends Component
{
    public $search = '';

    public function mount()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Only Super Admins can select institutions.');
        }
    }

    public function selectTenant($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        if (!$tenant->isActive()) {
            session()->flash('error', 'Selected institution is inactive.');
            return;
        }

        session(['tenant_id' => $tenant->id]);
        
        return redirect()->route('dashboard')->with('success', "Switched to {$tenant->name}");
    }

    public function render()
    {
        $tenants = Tenant::active()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('subdomain', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('name')
            ->get();

        return view('livewire.select-tenant', [
            'tenants' => $tenants
        ])->layout('layouts.app');
    }
}
