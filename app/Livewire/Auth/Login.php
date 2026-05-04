<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $tenant_code;
    public $username;
    public $password;
    public $remember = false;

    protected $rules = [
        'tenant_code' => 'required',
        'username' => 'required',
        'password' => 'required',
    ];

    public function login()
    {
        $this->validate();

        $tenant = \App\Models\Tenant::where('code', $this->tenant_code)->first();

        if (!$tenant) {
            $this->addError('tenant_code', 'Invalid Institution Code.');
            return;
        }

        // Domain Hijack Prevention: Ensure the typed code matches the school's actual web domain!
        $activeDomainTenant = config('tenant.current_id');
        if ($activeDomainTenant && $activeDomainTenant !== $tenant->id) {
            $this->addError('tenant_code', 'Security Block: You cannot log into this institution from a different school\'s web address (subdomain).');
            return;
        }

        // 2. Find user without global scopes to bypass the "catch-22" lockdown
        $user = \App\Models\User::withoutGlobalScopes()
                    ->where(function($query) {
                        $query->where('username', $this->username)
                              ->orWhere('email', $this->username);
                    })
                    ->where('tenant_id', $tenant->id)
                    ->first();

        if (!$user) {
            $this->addError('username', 'User not found in this institution.');
            return;
        }

        // 3. Attempt login with the found user
        if (\Illuminate\Support\Facades\Hash::check($this->password, $user->password)) {
            Auth::login($user, $this->remember);
            session()->regenerate();
            session(['tenant_id' => $tenant->id]);
            return redirect()->intended('/dashboard');
        }

        $this->addError('password', 'Invalid password.');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}
