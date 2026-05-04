<div class="min-vh-100 d-flex align-items-center justify-content-center bg-login">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden login-card backdrop-blur">
        <div class="row g-0">
            <div class="col-12 p-5">
                <!-- Brand/Logo Area -->
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-gradient rounded-circle mb-3 shadow" style="width: 80px; height: 80px;">
                        <i class="fas fa-university text-white fs-1"></i>
                    </div>
                    <h2 class="fw-bold text-dark mb-1">Edubase SaaS</h2>
                    <p class="text-muted small">Academic Management System <span class="badge bg-soft-success text-success px-2 py-1 x-small ms-1">v3.0</span></p>
                </div>

                <!-- Login Form -->
                <form wire:submit.prevent="login">
                    <!-- Institution Code -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Institution Code</label>
                        <div class="input-group border rounded-3 overflow-hidden transition-all shadow-sm">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-building text-primary"></i></span>
                            <input wire:model="tenant_code" type="text" class="form-control border-0 py-2 shadow-none" placeholder="e.g. ALH-101">
                        </div>
                        @error('tenant_code') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted text-uppercase">Account Identifier</label>
                        <div class="input-group border rounded-3 overflow-hidden transition-all shadow-sm">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-user-circle text-primary"></i></span>
                            <input wire:model="username" type="text" class="form-control border-0 py-2 shadow-none" placeholder="Username or Email">
                        </div>
                        @error('username') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold text-muted text-uppercase">Security Key</label>
                            <a href="#" class="text-primary text-decoration-none x-small fw-bold">Forgot?</a>
                        </div>
                        <div class="input-group border rounded-3 overflow-hidden transition-all shadow-sm">
                            <span class="input-group-text bg-white border-0"><i class="fas fa-key text-primary"></i></span>
                            <input id="login-password-field" name="password" wire:model="password" type="password" class="form-control border-0 py-2 shadow-none" placeholder="••••••••">
                            <span class="input-group-text bg-white border-0 cursor-pointer text-muted" onclick="var p=document.getElementById('login-password-field');var i=this.querySelector('i');if(p.type==='password'){p.type='text';i.classList.remove('fa-eye');i.classList.add('fa-eye-slash');}else{p.type='password';i.classList.remove('fa-eye-slash');i.classList.add('fa-eye');}">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('password') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 d-flex align-items-center">
                        <div class="form-check">
                            <input wire:model.defer="remember" class="form-check-input cursor-pointer" type="checkbox" id="rememberMe">
                            <label class="form-check-label small text-muted cursor-pointer" for="rememberMe">
                                Stay signed in on this device
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm transition-all hover-lift d-flex align-items-center justify-content-center">
                        <span wire:loading.remove>Access Management Portal</span>
                        <span wire:loading class="spinner-border spinner-border-sm me-2"></span>
                        <i class="fas fa-arrow-right ms-2" wire:loading.remove></i>
                    </button>
                </form>

                <div class="text-center mt-5">
                    <p class="text-muted small mb-0">&copy; {{ date('Y') }} Al-hikma Solutions. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-login {
            background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
        }
        .login-card { width: 100%; max-width: 480px; }
        .backdrop-blur { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .bg-soft-success { background-color: rgba(28, 200, 138, 0.1); }
        .x-small { font-size: 0.75rem; }
        .transition-all { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-2px); }
        .cursor-pointer { cursor: pointer; }
    </style>
</div>
