<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Campus\Campus;
use App\Models\Finance\GLAccount;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UsersAndPermissions extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search_id = '';
    public $search_username = '';
    public $search_status = '';
    public $search_role = '';

    // Core Fields
    public $username, $name, $email, $role = 'User', $status = 'active', $password, $contact, $power_level;
    public $editing_id = null;

    // Permissions Fields
    public $allowed_campuses = [];
    public $allowed_accounts = [];
    public $user_roles = []; // New dynamic roles selection
    public $payment_date_restriction = 'current_day';
    public $custom_date_restriction = '';
    public $two_step_approval = false;
    public $global_cash_accounts = []; // The whitelist for the tenant

    public function mount()
    {
        $tenant_id = session('tenant_id');
        $globalSetting = \App\Models\Finance\TenantSetting::where('tenant_id', $tenant_id)
            ->where('key', 'allowed_cash_accounts')
            ->first();

        $this->global_cash_accounts = $globalSetting ? $globalSetting->value : [];
    }

    public function resetFields()
    {
        $this->username = ''; $this->name = ''; $this->email = '';
        $this->role = 'User'; $this->status = 'active'; $this->password = '';
        $this->contact = ''; $this->power_level = ''; $this->editing_id = null;
        $this->allowed_campuses = []; $this->allowed_accounts = []; $this->user_roles = [];
        $this->payment_date_restriction = 'current_day'; $this->custom_date_restriction = '';
        $this->two_step_approval = false;
    }

    public function save()
    {
        $rules = [
            'username' => 'required|unique:users,username,' . $this->editing_id,
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $this->editing_id,
            'role' => 'required',
        ];

        if (!$this->editing_id) { $rules['password'] = 'required|min:6'; }
        $this->validate($rules);

        DB::transaction(function() {
            $preferences = [
                'payment_date' => $this->payment_date_restriction,
                'custom_date' => $this->custom_date_restriction,
                'two_step_approval' => $this->two_step_approval
            ];

            // Sync legacy role field with the first selected dynamic role
            if (!empty($this->user_roles)) {
                $firstRole = Role::find($this->user_roles[0]);
                if ($firstRole) {
                    $this->role = $firstRole->name;
                }
            }

            $userData = [
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,
                'contact' => $this->contact,
                'power_level' => $this->power_level,
                'preferences' => $preferences,
            ];

            if ($this->password) { $userData['password'] = Hash::make($this->password); }

            if ($this->editing_id) {
                $user = User::findOrFail($this->editing_id);
                $userData['updated_by'] = Auth::id();
                $user->update($userData);
            } else {
                $userData['tenant_id'] = session('tenant_id');
                $userData['created_by'] = Auth::id();
                $userData['updated_by'] = Auth::id();
                $user = User::create($userData);
            }

            // Sync Permissions
            DB::table('user_campus_permissions')->where('user_id', $user->id)->delete();
            foreach($this->allowed_campuses as $campusId => $allowed) {
                if ($allowed) {
                    DB::table('user_campus_permissions')->insert([
                        'user_id' => $user->id, 'campus_id' => $campusId, 'is_allowed' => true
                    ]);
                }
            }

            DB::table('user_account_permissions')->where('user_id', $user->id)->delete();
            foreach($this->allowed_accounts as $accountId => $allowed) {
                if ($allowed) {
                    DB::table('user_account_permissions')->insert([
                        'user_id' => $user->id, 'account_id' => $accountId, 'is_allowed' => true
                    ]);
                }
            }

            // Sync Dynamic Roles
            $user->roles()->sync($this->user_roles);
        });

        $this->resetFields();
        session()->flash('message', 'User account and permissions processed successfully.');
        $this->dispatch('closeModal');
    }

    public function saveGlobalSettings()
    {
        if (!auth()->check() || (!auth()->user()->isSuperAdmin() && auth()->user()->role != 'Supper Admin')) {
            return;
        }

        \App\Models\Finance\TenantSetting::updateOrCreate(
            ['tenant_id' => session('tenant_id'), 'key' => 'allowed_cash_accounts'],
            [
                'value' => $this->global_cash_accounts,
                'updated_by' => Auth::id()
            ]
        );

        session()->flash('message', 'Global cash account permissions updated.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->editing_id = $user->id;
        $this->username = $user->username;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->status = $user->status;
        $this->contact = $user->contact;
        $this->power_level = $user->power_level;

        // Auto-suggest username if blank to prevent validation lockout
        if (empty($this->username)) {
            $this->username = \Illuminate\Support\Str::slug($this->name);
        }
        
        $prefs = $user->preferences ?: [];
        $this->payment_date_restriction = $prefs['payment_date'] ?? 'current_day';
        $this->custom_date_restriction = $prefs['custom_date'] ?? '';
        $this->two_step_approval = $prefs['two_step_approval'] ?? false;

        $this->allowed_campuses = DB::table('user_campus_permissions')->where('user_id', $id)
                                    ->pluck('is_allowed', 'campus_id')->toArray();
        $this->allowed_accounts = DB::table('user_account_permissions')->where('user_id', $id)
                                    ->pluck('is_allowed', 'account_id')->toArray();
        
        $this->user_roles = $user->roles()->pluck('roles.id')->map(fn($id) => (string)$id)->toArray();
        $this->dispatch('user-edit-loaded', roles: $this->user_roles);
    }

    public function render()
    {
        $tenant_id = session('tenant_id') ?? config('tenant.current_id') ?? auth()->user()->tenant_id;
        $globalSetting = \App\Models\Finance\TenantSetting::where('tenant_id', $tenant_id)
            ->where('key', 'allowed_cash_accounts')
            ->first();

        $query = User::query()
            ->where('tenant_id', $tenant_id)
            ->when($this->search_id, fn($q) => $q->where('id', 'LIKE', "%{$this->search_id}%"))
            ->when($this->search_username, fn($q) => $q->where('username', 'LIKE', "%{$this->search_username}%")
                                                     ->orWhere('name', 'LIKE', "%{$this->search_username}%"))
            ->when($this->search_status, fn($q) => $q->where('status', $this->search_status))
            ->when($this->search_role, fn($q) => $q->where('role', $this->search_role))
            ->with('roles')
            ->orderBy('id', 'asc');

        $all_cash_accounts = GLAccount::whereIn('type', ['asset', 'income', 'expense', 'liability', 'cash', 'bank'])->get();
        
        // Filter the accounts shown in the user permission table based on the global whitelist
        $filtered_accounts = $all_cash_accounts;
        if (!empty($this->global_cash_accounts)) {
            $filtered_accounts = $all_cash_accounts->whereIn('id', $this->global_cash_accounts);
        }

        return view('livewire.users-and-permissions', [
            'users' => $query->paginate(10),
            'campuses' => Campus::all(),
            'cash_accounts' => $filtered_accounts,
            'all_system_accounts' => $all_cash_accounts,
            'system_roles' => Role::all() // Load dynamic roles
        ])->layout('layouts.app');
    }
}

