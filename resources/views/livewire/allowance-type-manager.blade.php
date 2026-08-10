<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-money-check-alt me-2 text-primary"></i> Allowance / Deduction Types</h2>
        <button wire:click="openModal" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Type</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allowanceTypes as $allowanceType)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $allowanceType->name }}</td>
                            <td>
                                <span class="badge {{ $allowanceType->type === 'allowance' ? 'bg-success' : 'bg-danger' }}">
                                    {{ ucfirst($allowanceType->type) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $allowanceType->description ?: '—' }}</td>
                            <td class="text-end pe-4">
                                <button wire:click="edit({{ $allowanceType->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $allowanceType->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-money-check-alt mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Allowance/Deduction Types Found</h5>
                                <p>E.g. Medical Allowance, House Rent, Provident Fund.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $allowanceTypes->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $allowance_type_id ? 'Edit Type' : 'Create Type' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg bg-light" wire:model="name" placeholder="E.g. Medical Allowance">
                  @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="mb-3">
                  <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                  <select class="form-select bg-light" wire:model="type">
                      <option value="allowance">Allowance</option>
                      <option value="deduction">Deduction</option>
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-bold">Description</label>
                  <input type="text" class="form-control bg-light" wire:model="description">
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
