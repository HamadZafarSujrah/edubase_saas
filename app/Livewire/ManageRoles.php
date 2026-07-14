<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Role;
use App\Models\Permission;

class ManageRoles extends Component
{
    public $role_name;
    public $role_description;

    public $selectedRoleId = null;
    public $rolePermissions = [];
    public $selectAll = false;

    public function rules()
    {
        return [
            'role_name' => [
                'required', 
                'min:3', 
                \Illuminate\Validation\Rule::unique('roles', 'name')->where(function ($query) {
                    return $query->where('tenant_id', session('tenant_id'));
                })
            ],
            'role_description' => 'nullable|string|max:255'
        ];
    }

    public function mount()
    {
        // Auto-select first role for the UI if it exists
        $firstRole = Role::where('tenant_id', session('tenant_id'))->first();
        if ($firstRole) {
            $this->selectRole($firstRole->id);
        }
    }

    public function selectRole($id)
    {
        $this->selectedRoleId = $id;
        $role = Role::find($id);
        if ($role) {
            $this->rolePermissions = $role->permissions()->pluck('permissions.id')->map(fn($pid) => (string)$pid)->toArray();
            $this->selectAll = count($this->rolePermissions) === Permission::count();
        } else {
            $this->rolePermissions = [];
            $this->selectAll = false;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->rolePermissions = Permission::pluck('id')->map(fn($pid) => (string)$pid)->toArray();
        } else {
            $this->rolePermissions = [];
        }
    }

    public function toggleModule($module)
    {
        $permissionsInModule = Permission::where('module', $module)->pluck('id')->map(fn($pid) => (string)$pid)->toArray();
        $hasAll = count(array_intersect($permissionsInModule, $this->rolePermissions)) === count($permissionsInModule);

        if ($hasAll) {
            // Remove all if they are already all checked
            $this->rolePermissions = array_diff($this->rolePermissions, $permissionsInModule);
        } else {
            // Add all
            $this->rolePermissions = array_unique(array_merge($this->rolePermissions, $permissionsInModule));
        }

        $this->selectAll = count($this->rolePermissions) === Permission::count();
    }

    public function updateRolePermissions()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('roles.manage')) {
            session()->flash('error', 'You are not authorized to manage roles.');
            return;
        }

        if (!$this->selectedRoleId) return;

        $role = Role::find($this->selectedRoleId);
        if ($role) {
            // Because rolePermissions might contain strings or booleans from checkboxes, ensure we sync an array of IDs
            $role->permissions()->sync($this->rolePermissions);
            session()->flash('message', 'Role permissions updated securely.');
        }
    }

    public function saveRole()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('roles.manage')) {
            session()->flash('error', 'You are not authorized to manage roles.');
            return;
        }

        $this->validate();

        $role = Role::create([
            'tenant_id' => session('tenant_id'),
            'name' => $this->role_name,
            'slug' => \Illuminate\Support\Str::slug($this->role_name),
            'description' => $this->role_description,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset(['role_name', 'role_description']);
        $this->dispatch('closeModal');
        $this->selectRole($role->id);
        session()->flash('message', 'New role has been successfully created.');
    }

    public function render()
    {
        return view('livewire.manage-roles', [
            'roles' => Role::where('tenant_id', session('tenant_id'))->get(),
            'permissions' => Permission::all()
        ])->layout('layouts.app');
    }
}
