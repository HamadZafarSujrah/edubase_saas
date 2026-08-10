<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-medal me-2 text-primary"></i> Employee Standings</h2>
        <button wire:click="openModal" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Standing</button>
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
                            <th class="ps-4">Standing</th>
                            <th>Description</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($standings as $standing)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $standing->name }}</td>
                            <td class="text-muted">{{ $standing->description ?: '—' }}</td>
                            <td class="text-end pe-4">
                                <button wire:click="edit({{ $standing->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $standing->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="fas fa-medal mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Standings Found</h5>
                                <p>E.g. Excellent, Satisfactory, Needs Improvement.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $standings->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $standing_id ? 'Edit Standing' : 'Create Standing' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg bg-light" wire:model="name" placeholder="E.g. Excellent">
                  @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
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
