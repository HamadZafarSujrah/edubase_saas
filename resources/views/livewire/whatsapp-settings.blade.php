<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-envelope-open-text me-2 text-primary"></i> WhatsApp Service Settings</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">WhatsApp Service Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="alert alert-warning border-0 small mb-4">
                <i class="fas fa-tools me-2"></i> WhatsApp sending is not yet connected to a live provider. Save your business number and API key here now so it's ready the moment WhatsApp integration goes live — nothing is sent from this screen.
            </div>

            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" wire:model="is_enabled" id="waEnabled">
                <label class="form-check-label fw-bold" for="waEnabled">Enable WhatsApp notifications (once available)</label>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Business Number</label>
                    <input type="text" class="form-control bg-light" wire:model="business_number" placeholder="+92 3XX XXXXXXX">
                    @error('business_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">API Key</label>
                    <input type="password" class="form-control bg-light" wire:model="api_key" placeholder="••••••••">
                    @error('api_key') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <button wire:click="save" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                <i class="fas fa-save me-2"></i> Save Settings
            </button>
        </div>
    </div>
</div>
