<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-calendar-alt me-2 text-primary"></i> Academic Sessions</h2>
        <button wire:click="openModal()" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Session</button>
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
                            <th class="ps-4">Session Title</th>
                            <th>Duration (Start - End)</th>
                            <th>Status (Active Year)</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $session->name }}</td>
                            <td>
                                <span class="text-muted"><i class="far fa-calendar me-1"></i> {{ $session->start_date->format('M d, Y') }}</span>
                                <span class="mx-2 text-muted">-</span>
                                <span class="text-muted"><i class="far fa-calendar-check me-1"></i> {{ $session->end_date->format('M d, Y') }}</span>
                            </td>
                            <td>
                                @if($session->is_active)
                                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Current Session</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">Past/Upcoming</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="edit({{ $session->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $session->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Academic Sessions Established</h5>
                                <p>You must create an academic year (e.g. 2024-2025) before admitting any students.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $sessions->links() }}
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-calendar-plus text-primary me-2"></i>{{ $session_id ? 'Edit Academic Session' : 'Create Academic Session' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal()"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-4">
                  <label class="form-label fw-bold">Session Name/Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg bg-light" wire:model="name" placeholder="E.g. 2024-2025">
                  @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="row">
                  <div class="col-6 mb-4">
                      <label class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                      <input type="date" class="form-control bg-light" wire:model="start_date">
                      @error('start_date') <span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-6 mb-4">
                      <label class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                      <input type="date" class="form-control bg-light" wire:model="end_date">
                      @error('end_date') <span class="text-danger small">{{ $message }}</span>@enderror
                  </div>
              </div>
              
              <div class="alert alert-warning py-2 mb-0">
                  <div class="form-check form-switch m-0 ps-3">
                      <input type="checkbox" class="form-check-input" wire:model="is_active" id="isActiveSessionCheck" style="margin-left: -2em;">
                      <label class="form-check-label fw-bold ms-2 text-dark" for="isActiveSessionCheck">Make this the Current Active Session</label>
                  </div>
                  <small class="d-block text-muted mt-1 ps-4">Warning: Checking this will automatically disable the previously active session.</small>
              </div>

          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save()">Save Session</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
