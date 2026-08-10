<div class="container-fluid">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-dark text-center">Manage Discount Types</h5>
        </div>
        <div class="card-body p-4">

            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <div class="p-4 bg-light rounded-4 border">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Discount Name</label>
                                <input type="text" wire:model="name" class="form-control border-0 shadow-none px-3 py-2" placeholder="e.g. Sibling Discount, Merit Scholarship...">
                                @error('name') <span class="text-danger tiny fw-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Type</label>
                                <select wire:model="type" class="form-select border-0 shadow-none px-3 py-2">
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percent">Percentage</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActiveSwitch">
                                    <label class="form-check-label small fw-bold" for="isActiveSwitch">Active</label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            @if($editing_id)
                                <button wire:click="update" class="btn btn-warning px-4 fw-bold">Update</button>
                                <button wire:click="$reset(['editing_id', 'name', 'type', 'is_active'])" class="btn btn-light px-3 border">Cancel</button>
                            @else
                                <button wire:click="create" class="btn btn-success px-4 fw-bold"><i class="fas fa-plus me-1"></i> Create</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm ps-4 py-2">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm ps-4 py-2">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-3">#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discountTypes as $index => $d)
                            <tr>
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $d->name }}</td>
                                <td class="small text-muted">{{ $d->type === 'percent' ? 'Percentage' : 'Fixed Amount' }}</td>
                                <td>
                                    @if($d->is_active)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <button wire:click="edit({{ $d->id }})" class="btn btn-outline-primary btn-sm rounded-circle"><i class="fas fa-edit"></i></button>
                                    <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $d->id }})" class="btn btn-outline-danger btn-sm rounded-circle ms-1"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">No discount types defined yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
