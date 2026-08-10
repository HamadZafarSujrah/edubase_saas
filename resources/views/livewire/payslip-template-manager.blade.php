<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice me-2 text-primary"></i> Manage Print Template</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Manage Print Template</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <h6 class="fw-bold text-primary mb-0">Payslip Template</h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="small fw-bold text-muted mb-1">Header Color</label>
                    <input type="color" wire:model="header_color" class="form-control form-control-lg border-0 bg-light shadow-sm" style="height: 48px;">
                    @error('header_color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-8">
                    <label class="small fw-bold text-muted mb-1">Footer Text</label>
                    <input type="text" wire:model="footer_text" class="form-control border-0 bg-light shadow-sm" placeholder="E.g. This is a system-generated payslip.">
                    @error('footer_text') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" wire:model="show_logo" id="showLogoCheck">
                        <label class="form-check-label small fw-bold text-muted" for="showLogoCheck">Show school logo on payslip</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <button wire:click="save" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                <i class="fas fa-save me-2"></i> Save Template
            </button>
        </div>
    </div>
</div>
