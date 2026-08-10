<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Finance\TenantSetting;
use App\Services\TenantProvisioningService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TenantManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $status_filter = '';

    // Create/edit modal
    public $isModalOpen = false;
    public $tenant_id;
    public $name, $code, $subdomain, $email, $phone, $address, $status = 'active';
    public $logo_url, $primary_color = '#1e293b';
    public $plan_id, $subscription_status = 'trial', $trial_ends_at;

    // Create-only fields (first tenant_super_admin)
    public $admin_name, $admin_username, $admin_email, $admin_password;

    // Modules modal
    public $isModulesModalOpen = false;
    public $modulesTenantId;
    public $enabled_modules = [];

    public $availableModules = [
        'academic' => 'Academic (Classes, Sections, Admissions)',
        'finance' => 'Finance & Fee Challans',
        'exam' => 'Exam & Attendance',
        'hrm' => 'HRM (Employees, Payroll)',
        'general' => 'General & House Management',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() || $user->tenant_id) {
            abort(403, 'Only platform-level super admins can manage institutions.');
        }
    }

    public function render()
    {
        $query = Tenant::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('subdomain', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status_filter) {
            $query->where('status', $this->status_filter);
        }

        return view('livewire.tenant-manager', [
            'tenants' => $query->with('plan')->orderBy('name')->paginate(10),
            'plans' => Plan::where('is_active', true)->orderBy('price')->get(),
        ])->layout('layouts.app');
    }

    public function openModal() { $this->isModalOpen = true; }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->tenant_id = null;
        $this->name = '';
        $this->code = '';
        $this->subdomain = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->status = 'active';
        $this->logo_url = '';
        $this->primary_color = '#1e293b';
        $this->plan_id = '';
        $this->subscription_status = 'trial';
        $this->trial_ends_at = '';
        $this->admin_name = '';
        $this->admin_username = '';
        $this->admin_email = '';
        $this->admin_password = '';
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenant_id = $tenant->id;
        $this->name = $tenant->name;
        $this->code = $tenant->code;
        $this->subdomain = $tenant->subdomain;
        $this->email = $tenant->email;
        $this->phone = $tenant->phone;
        $this->address = $tenant->address;
        $this->status = $tenant->status;
        $this->logo_url = $tenant->logo_url;
        $this->primary_color = $tenant->primary_color ?? '#1e293b';
        $this->plan_id = $tenant->plan_id;
        $this->subscription_status = $tenant->subscription_status;
        $this->trial_ends_at = $tenant->trial_ends_at?->format('Y-m-d');
        $this->openModal();
    }

    public function save()
    {
        $tenantRules = [
            'name' => 'required|string|max:150',
            'code' => ['required', 'string', 'max:50', Rule::unique('tenants', 'code')->ignore($this->tenant_id)],
            'subdomain' => ['required', 'string', 'max:63', 'regex:/^[a-z0-9-]+$/', Rule::unique('tenants', 'subdomain')->ignore($this->tenant_id)],
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,suspended',
            'logo_url' => 'nullable|string|max:255',
            'primary_color' => 'nullable|regex:/^#[0-9A-Fa-f]{6}$/',
            'plan_id' => 'nullable|exists:plans,id',
            'subscription_status' => 'required|in:trial,active,past_due,cancelled',
            'trial_ends_at' => 'nullable|date',
        ];

        if (!$this->tenant_id) {
            $tenantRules['admin_name'] = 'required|string|max:150';
            $tenantRules['admin_username'] = 'required|string|max:50|unique:users,username';
            $tenantRules['admin_email'] = 'required|email|unique:users,email';
            $tenantRules['admin_password'] = 'required|string|min:8';
        }

        $this->validate($tenantRules, [
            'code.unique' => 'This institution code is already in use.',
            'subdomain.unique' => 'This subdomain is already in use.',
            'subdomain.regex' => 'Subdomain may only contain lowercase letters, numbers, and hyphens.',
            'admin_username.unique' => 'This username is already taken.',
            'admin_email.unique' => 'This email is already registered.',
        ]);

        if ($this->tenant_id) {
            Tenant::findOrFail($this->tenant_id)->update([
                'name' => $this->name,
                'code' => $this->code,
                'subdomain' => $this->subdomain,
                'email' => $this->email,
                'phone' => $this->phone,
                'address' => $this->address,
                'status' => $this->status,
                'logo_url' => $this->logo_url ?: null,
                'primary_color' => $this->primary_color ?: null,
                'plan_id' => $this->plan_id ?: null,
                'subscription_status' => $this->subscription_status,
                'trial_ends_at' => $this->trial_ends_at ?: null,
            ]);

            session()->flash('message', 'Institution updated successfully.');
        } else {
            app(TenantProvisioningService::class)->provision([
                'name' => $this->name,
                'code' => $this->code,
                'subdomain' => $this->subdomain,
                'email' => $this->email,
                'admin_name' => $this->admin_name,
                'admin_username' => $this->admin_username,
                'admin_email' => $this->admin_email,
                'admin_password' => $this->admin_password,
            ]);

            session()->flash('message', 'Institution created successfully. The admin can now log in with the code "' . $this->code . '".');
        }

        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['status' => $tenant->status === 'active' ? 'suspended' : 'active']);
        session()->flash('message', $tenant->status === 'active' ? 'Institution activated.' : 'Institution suspended.');
    }

    public function openModulesModal($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->modulesTenantId = $id;

        $setting = TenantSetting::where('tenant_id', $id)->where('key', 'enabled_modules')->first();
        $this->enabled_modules = ($setting && is_array($setting->value))
            ? $setting->value
            : array_keys($this->availableModules);

        $this->isModulesModalOpen = true;
    }

    public function closeModulesModal()
    {
        $this->isModulesModalOpen = false;
        $this->modulesTenantId = null;
        $this->enabled_modules = [];
    }

    public function saveModules()
    {
        TenantSetting::updateOrCreate(
            ['tenant_id' => $this->modulesTenantId, 'key' => 'enabled_modules'],
            ['value' => $this->enabled_modules, 'updated_by' => Auth::id()]
        );

        session()->flash('message', 'Module access updated successfully.');
        $this->closeModulesModal();
    }
}
