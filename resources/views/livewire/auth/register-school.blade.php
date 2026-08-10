<div class="min-vh-100 d-flex align-items-center justify-content-center bg-login py-5">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden register-card backdrop-blur">
        <div class="p-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-gradient rounded-circle mb-3 shadow" style="width: 80px; height: 80px;">
                    <i class="fas fa-school text-white fs-1"></i>
                </div>
                <h2 class="fw-bold text-dark mb-1">Register Your School</h2>
                <p class="text-muted small">Get your own EduBase workspace in a minute.</p>
            </div>

            <form wire:submit.prevent="register">
                <h6 class="fw-bold text-muted small text-uppercase mb-3">School Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">School Name <span class="text-danger">*</span></label>
                        <input wire:model.live.debounce.500ms="school_name" type="text" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('school_name') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Login Code <span class="text-danger">*</span></label>
                        <input wire:model="code" type="text" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('code') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Subdomain <span class="text-danger">*</span></label>
                        <input wire:model="subdomain" type="text" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('subdomain') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">School Email</label>
                        <input wire:model="email" type="email" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('email') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <h6 class="fw-bold text-muted small text-uppercase mb-3">Your Admin Account</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Full Name <span class="text-danger">*</span></label>
                        <input wire:model="admin_name" type="text" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('admin_name') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Username <span class="text-danger">*</span></label>
                        <input wire:model="admin_username" type="text" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('admin_username') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Email <span class="text-danger">*</span></label>
                        <input wire:model="admin_email" type="email" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('admin_email') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Password <span class="text-danger">*</span></label>
                        <input wire:model="admin_password" type="password" class="form-control border rounded-3 py-2 shadow-sm">
                        @error('admin_password') <span class="text-danger x-small mt-1 d-block">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Confirm Password <span class="text-danger">*</span></label>
                        <input wire:model="admin_password_confirmation" type="password" class="form-control border rounded-3 py-2 shadow-sm">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center">
                    <span wire:loading.remove>Create My School Workspace</span>
                    <span wire:loading class="spinner-border spinner-border-sm me-2"></span>
                    <i class="fas fa-arrow-right ms-2" wire:loading.remove></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-primary text-decoration-none small fw-bold">Already have an account? Log in</a>
            </div>
        </div>
    </div>

    <style>
        .bg-login {
            background: linear-gradient(45deg, #f8f9fa 0%, #e9ecef 100%);
            background-image: url('https://www.transparenttextures.com/patterns/cubes.png');
        }
        .register-card { width: 100%; max-width: 720px; }
        .backdrop-blur { backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .x-small { font-size: 0.75rem; }
    </style>
</div>
