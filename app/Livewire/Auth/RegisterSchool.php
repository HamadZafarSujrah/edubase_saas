<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Services\TenantProvisioningService;

class RegisterSchool extends Component
{
    public $school_name = '';
    public $code = '';
    public $subdomain = '';
    public $email = '';

    public $admin_name = '';
    public $admin_username = '';
    public $admin_email = '';
    public $admin_password = '';
    public $admin_password_confirmation = '';

    public function updatedSchoolName()
    {
        if ($this->code === '' ) {
            $this->code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $this->school_name));
        }
        if ($this->subdomain === '') {
            $this->subdomain = strtolower(preg_replace('/[^a-z0-9]+/', '-', trim(strtolower($this->school_name))));
            $this->subdomain = trim($this->subdomain, '-');
        }
    }

    public function register()
    {
        $this->validate([
            'school_name' => 'required|string|max:150',
            'code' => 'required|string|max:50|unique:tenants,code',
            'subdomain' => 'required|string|max:63|regex:/^[a-z0-9-]+$/|unique:tenants,subdomain',
            'email' => 'nullable|email|max:255',
            'admin_name' => 'required|string|max:150',
            'admin_username' => 'required|string|max:50|unique:users,username',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ], [
            'code.unique' => 'That institution code is taken. Please choose another.',
            'subdomain.unique' => 'That subdomain is taken. Please choose another.',
            'subdomain.regex' => 'Subdomain may only contain lowercase letters, numbers, and hyphens.',
            'admin_username.unique' => 'That username is already taken.',
            'admin_email.unique' => 'That email is already registered.',
        ]);

        $result = app(TenantProvisioningService::class)->provision([
            'name' => $this->school_name,
            'code' => $this->code,
            'subdomain' => $this->subdomain,
            'email' => $this->email,
            'admin_name' => $this->admin_name,
            'admin_username' => $this->admin_username,
            'admin_email' => $this->admin_email,
            'admin_password' => $this->admin_password,
        ]);

        return redirect()->route('login')->with(
            'success',
            "Your school \"{$result['tenant']->name}\" is ready! Log in with Institution Code \"{$result['tenant']->code}\" and your new username."
        );
    }

    public function render()
    {
        return view('livewire.auth.register-school')->layout('layouts.guest');
    }
}
