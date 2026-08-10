<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-broadcast-tower me-2 text-primary"></i> SMS Gateway Settings</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">SMS Gateway Settings</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4"><i class="fas fa-check-circle me-2"></i>{{ session('message') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="alert alert-info border-0 small mb-4">
                <i class="fas fa-info-circle me-2"></i> SMS sending currently runs in <strong>mock mode</strong> (logged, not actually delivered) until a real gateway is connected. Your settings here are saved and used by every SMS feature (SMS Blaster, attendance/result notifications) the moment a real provider is wired up.
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Provider</label>
                    <select class="form-select bg-light" wire:model="provider">
                        <option value="mock">Mock (Testing Only)</option>
                        <option value="twilio">Twilio</option>
                        <option value="local">Local Provider</option>
                    </select>
                    @error('provider') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Sender ID</label>
                    <input type="text" class="form-control bg-light" wire:model="sender_id" placeholder="E.g. EDUBASE">
                    @error('sender_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
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
