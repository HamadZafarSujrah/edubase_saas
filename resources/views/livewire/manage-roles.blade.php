<div class="container-fluid py-4 bg-light min-vh-100">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-5 border-primary">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Roles & Permissions Control</h1>
            <p class="text-muted mb-0 small"><i class="fas fa-shield-alt me-2"></i>Access Level Management System</p>
        </div>
        <div>
            <button class="btn btn-primary px-4 py-2 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newRoleModal">
                <i class="fas fa-plus-circle me-2"></i> Create New Role
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Roles List -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-header bg-dark text-white p-4">
                    <h5 class="mb-0 fw-bold">Active Roles</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($roles as $role)
                    <a href="#" wire:click.prevent="selectRole({{ $role->id }})" class="list-group-item list-group-item-action p-4 border-0 border-bottom {{ $selectedRoleId == $role->id ? 'active' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="fw-bold mb-1 {{ $selectedRoleId == $role->id ? '' : 'text-dark' }}">{{ $role->name }}</h6>
                                <p class="small mb-0 {{ $selectedRoleId == $role->id ? 'text-white-50' : 'text-muted' }}">{{ $role->description ?? 'No description' }}</p>
                            </div>
                            <span class="badge {{ $selectedRoleId == $role->id ? 'bg-white text-primary' : 'bg-light text-dark border' }} rounded-pill px-3">{{ DB::table('user_roles')->where('role_id', $role->id)->count() }} Users</span>
                        </div>
                    </a>
                    @empty
                    <div class="p-4 text-center text-muted small">No roles found in the system.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Permission Matrix -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold text-dark mb-0">Permission Matrix: <span class="text-primary">{{ $roles->firstWhere('id', $selectedRoleId)->name ?? 'Select a Role' }}</span></h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input shadow-none cursor-pointer" type="checkbox" wire:model.live="selectAll" id="selectAll">
                        <label class="form-check-label small fw-bold cursor-pointer" for="selectAll">Select All</label>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr class="text-muted small uppercase letter-spacing-1 bg-light">
                                    <th class="py-3">Module Name</th>
                                    <th class="py-3 text-start">Permission</th>
                                    <th class="py-3 text-end pe-4">Access Toggle</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permissions->groupBy('module') as $module => $module_permissions)
                                    <tr>
                                        <td class="fw-bold text-primary bg-light border-bottom" colspan="2"><i class="fas fa-folder-open me-2 text-warning"></i> {{ $module ?: 'General' }}</td>
                                        <td class="bg-light border-bottom text-end pe-4 align-middle">
                                            @php
                                                $moduleIds = $module_permissions->pluck('id')->map(fn($id) => (string)$id)->toArray();
                                                $isModuleFullyChecked = count(array_intersect($moduleIds, $rolePermissions)) === count($moduleIds) && count($moduleIds) > 0;
                                            @endphp
                                            <div class="form-check form-switch d-inline-block m-0 p-0">
                                                <input type="checkbox" wire:click="toggleModule('{{ $module }}')" wire:key="module-{{ str_replace(' ', '-', $module) }}-{{ $isModuleFullyChecked ? 'on' : 'off' }}" class="form-check-input shadow-none cursor-pointer ms-0 float-end" style="width: 2.5rem; height: 1.25rem;" {{ $isModuleFullyChecked ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                    </tr>
                                    @foreach($module_permissions as $perm)
                                    <tr>
                                        <td></td>
                                        <td class="fw-bold text-dark">{{ $perm->name }}</td>
                                        <td class="text-end pe-4">
                                            <div class="form-check form-switch d-inline-block">
                                                <input type="checkbox" wire:model="rolePermissions" value="{{ $perm->id }}" class="form-check-input shadow-none cursor-pointer" style="width: 2.5rem; height: 1.25rem;">
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light p-4 d-flex justify-content-end align-items-center">
                    @if (session()->has('message'))
                        <div class="text-success fw-bold me-4 small">
                            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
                        </div>
                    @endif
                    <button wire:click="updateRolePermissions" wire:loading.attr="disabled" class="btn btn-dark rounded-pill px-5 fw-bold transition-all hover-lift" {{ !$selectedRoleId ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="updateRolePermissions"><i class="fas fa-save me-2 text-success"></i> Update Role Permissions</span>
                        <span wire:loading wire:target="updateRolePermissions"><i class="fas fa-spinner fa-spin me-2 text-success"></i> Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Role Modal -->
    <div wire:ignore.self class="modal fade" id="newRoleModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-dark text-white p-4">
                    <h5 class="fw-bold mb-0">Create New Role</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Role Name <span class="text-danger">*</span></label>
                        <input type="text" wire:model="role_name" class="form-control border shadow-none" placeholder="e.g. HR Manager">
                        @error('role_name') <span class="text-danger tiny fw-bold">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold">Description</label>
                        <input type="text" wire:model="role_description" class="form-control border shadow-none" placeholder="e.g. Can manage employees">
                    </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" wire:click="saveRole" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">Save Role</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('closeModal', event => {
            var el = document.getElementById('newRoleModal');
            var modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        });
    </script>

    <style>
        .uppercase { text-transform: uppercase; }
        .letter-spacing-1 { letter-spacing: 1px; }
        .list-group-item.active { background-color: #4e73df; border-color: #4e73df; }
        .transition-all { transition: all 0.3s ease; }
        .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important; }
        .form-check-input:checked { background-color: #198754; border-color: #198754; }
    </style>
</div>
