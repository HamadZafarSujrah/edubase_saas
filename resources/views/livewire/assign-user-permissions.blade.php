<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Breadcrumb -->
    <div class="bg-dark text-white py-2 px-3 rounded-2 mb-3 d-flex align-items-center shadow-sm" style="background-color: #2c3e50 !important;">
        <span class="small"><i class="fas fa-home me-2"></i>Home</span>
        <span class="mx-2 opacity-50">/</span>
        <span class="small fw-bold">Assign Module Permissions</span>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    <!-- User Info Card -->
    <div class="card shadow-sm border-0 rounded-3 mb-4 overflow-hidden">
        <div class="card-body bg-white p-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-4 rounded-circle me-4">
                        <i class="fas fa-user-shield text-primary fs-2"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold text-dark mb-1">{{ $user->name }}</h4>
                        <div class="d-flex gap-3">
                            <span class="badge bg-light text-dark border"><i class="fas fa-id-badge me-1 text-primary"></i> ID: {{ $user->id }}</span>
                            <span class="badge bg-light text-dark border"><i class="fas fa-at me-1 text-primary"></i> {{ $user->username ?: $user->email }}</span>
                            <span class="badge bg-primary px-3">{{ $user->role }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <button wire:click="toggleAll" class="btn btn-outline-primary px-4 py-2 small fw-bold shadow-sm me-2">
                        <i class="fas fa-check-double me-1"></i> Select/Deselect All
                    </button>
                    <a href="/users-and-permissions" class="btn btn-dark px-4 py-2 small ms-2"><i class="fas fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Matrix (Matching Screenshot) -->
    <div class="row justify-content-center">
        <div class="col-xl-10">
            @foreach($categories as $categoryName => $modules)
                <div class="card shadow-sm border-0 rounded-0 mb-4 overflow-hidden">
                    <div class="card-header py-1 d-flex justify-content-between align-items-center text-white fw-bold text-uppercase @if($loop->index % 4 == 0) bg-primary @elseif($loop->index % 4 == 1) bg-danger @elseif($loop->index % 4 == 2) bg-warning text-dark @else bg-success @endif">
                        <div style="flex: 1;"></div>
                        <h6 class="mb-0 fw-bold" style="flex: 2; text-align: center;">{{ $categoryName }}</h6>
                        <div style="flex: 1; text-align: right;" class="pe-3">
                             <div class="form-check form-switch d-inline-block header-toggle">
                                <input class="form-check-input shadow-none cursor-pointer" type="checkbox" role="button" wire:click="toggleAll('{{ $categoryName }}')" @if($this->isCategoryFull($categoryName)) checked @endif>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted tiny fw-bold border-bottom">
                                    <tr>
                                        <th width="5%" class="ps-4">No.</th>
                                        <th width="75%">Feature / Permission Module Name</th>
                                        <th width="20%" class="text-center pe-4">Permission Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $count = 1; @endphp
                                    @foreach($modules as $key => $label)
                                        <tr class="border-bottom">
                                            <td class="ps-4 text-muted small">{{ $count++ }}</td>
                                            <td class="fw-bold text-dark ps-2">{{ $label }}</td>
                                            <td class="text-center pe-4">
                                                <div class="form-check form-switch d-inline-block custom-switch">
                                                    <input class="form-check-input shadow-none cursor-pointer" type="checkbox" role="switch" wire:model="permissions.{{ $key }}">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Final Save Button -->
            <div class="text-center mt-5 mb-5 p-4 bg-white shadow-sm rounded-3 border-top border-primary border-3">
                <h5 class="fw-bold mb-3">Ready to update access for {{ $user->name }}?</h5>
                <button wire:click="save" wire:loading.attr="disabled" class="btn btn-emerald btn-lg px-5 shadow-sm fw-bold text-white transition">
                    <span wire:loading.remove>
                        <i class="fas fa-save me-1"></i> Save Permissions
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin me-1"></i> Saving...
                    </span>
                </button>
            </div>
        </div>
    </div>

<style>
    .bg-danger { background-color: #dc3545 !important; }
    .bg-primary { background-color: #0d6efd !important; }
    .bg-warning { background-color: #ffc107 !important; }
    .bg-success { background-color: #198754 !important; }
    .btn-emerald { background-color: #2a9d8f; border-color: #2a9d8f; color: white; transition: 0.3s; }
    .btn-emerald:hover { background-color: #21867a; border-color: #21867a; color: white; transform: translateY(-1px); }
    .tiny { font-size: 0.7rem; }
    .cursor-pointer { cursor: pointer; }

    /* Premium Header Toggle Fix */
    .header-toggle .form-check-input {
        width: 2.8em;
        height: 1.4em;
    }
    .header-toggle .form-check-input:not(:checked) {
        background-color: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .header-toggle .form-check-input:checked {
        background-color: #ffffff;
        border-color: #ffffff;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%230d6efd'/%3e%3c/svg%3e");
    }

    /* List Item Toggle Enhancement */
    .custom-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
    }
</style>
</div>

