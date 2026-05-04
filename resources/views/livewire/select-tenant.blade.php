<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold text-primary mb-2">Select Institution</h2>
                        <p class="text-muted">Choose a school to manage their data and operations.</p>
                    </div>

                    <!-- Search Box -->
                    <div class="mb-4">
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden">
                            <span class="input-group-text bg-white border-end-0 px-4">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input wire:model.live="search" type="text" 
                                   class="form-control border-start-0 ps-0 py-3 shadow-none" 
                                   placeholder="Search by name or subdomain...">
                        </div>
                    </div>

                    @if(session()->has('error'))
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <!-- Institution List -->
                    <div class="list-group list-group-flush gap-3">
                        @forelse($tenants as $tenant)
                            <button wire:click="selectTenant({{ $tenant->id }})" 
                                    class="list-group-item list-group-item-action border rounded-4 p-4 d-flex align-items-center justify-content-between transition-hover">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-4">
                                        <i class="fas fa-school text-primary fa-2x"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1 text-dark">{{ $tenant->name }}</h5>
                                        <div class="d-flex align-items-center gap-2 small text-muted">
                                            <span class="badge bg-light text-primary border fw-mono">{{ $tenant->subdomain }}</span>
                                            <span class="opacity-50">•</span>
                                            <span><i class="fas fa-envelope me-1"></i> {{ $tenant->email ?? 'No email' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-primary arrow-icon">
                                    <i class="fas fa-chevron-right fa-lg"></i>
                                </div>
                            </button>
                        @empty
                            <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                                <i class="fas fa-search fa-3x text-muted mb-3 opacity-25"></i>
                                <p class="text-muted mb-0">No institutions found matching your search.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
            
            <div class="text-center mt-4 text-muted small">
                Logged in as <span class="fw-bold">{{ auth()->user()->name }}</span> (Super Admin)
            </div>
        </div>
    </div>

    <style>
        .transition-hover {
            transition: all 0.2s ease;
        }
        .transition-hover:hover {
            transform: translateY(-2px);
            background-color: #f8fbff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05) !important;
        }
        .arrow-icon {
            opacity: 0;
            transition: all 0.2s ease;
            transform: translateX(-10px);
        }
        .transition-hover:hover .arrow-icon {
            opacity: 1;
            transform: translateX(0);
        }
        .fw-mono { font-family: monospace; }
        .rounded-4 { border-radius: 1rem !important; }
    </style>
</div>
