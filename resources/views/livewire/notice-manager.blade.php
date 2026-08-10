<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-bullhorn me-2 text-primary"></i> Notice Board</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Notice Board</li>
                </ol>
            </nav>
        </div>
        <button wire:click="openModal()" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Notice</button>
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
                            <th class="ps-4">Title</th>
                            <th>Campus</th>
                            <th>Published</th>
                            <th>Expiry</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notices as $notice)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $notice->title }}</td>
                            <td>{{ $notice->campus->name ?? 'All Campuses' }}</td>
                            <td class="text-muted">{{ $notice->published_at?->format('d-M-Y') }}</td>
                            <td class="text-muted">{{ $notice->expiry_date?->format('d-M-Y') ?? '—' }}</td>
                            <td>
                                @if($notice->is_active)
                                    <span class="badge bg-success rounded-pill">Active</span>
                                @else
                                    <span class="badge bg-secondary rounded-pill">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <button wire:click="edit({{ $notice->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $notice->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-bullhorn mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Notices Yet</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $notices->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $notice_id ? 'Edit Notice' : 'Create New Notice' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal()"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-4">
                  <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control form-control-lg bg-light" wire:model="title">
                  @error('title') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="mb-4">
                  <label class="form-label fw-bold">Body <span class="text-danger">*</span></label>
                  <textarea rows="5" class="form-control bg-light" wire:model="body"></textarea>
                  @error('body') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="row">
                  <div class="col-6 mb-4">
                      <label class="form-label fw-bold">Campus (Optional)</label>
                      <select class="form-select bg-light" wire:model="campus_id">
                          <option value="">All Campuses</option>
                          @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                      </select>
                  </div>
                  <div class="col-6 mb-4">
                      <label class="form-label fw-bold">Expiry Date (Optional)</label>
                      <input type="date" class="form-control bg-light" wire:model="expiry_date">
                  </div>
              </div>
              <div class="form-check form-switch form-control-lg ps-3 m-0">
                  <input type="checkbox" class="form-check-input" wire:model="is_active" id="noticeActiveCheck" style="margin-left: -2em;">
                  <label class="form-check-label fw-bold ms-2" for="noticeActiveCheck">Notice is Active</label>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal()">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save()">Save Notice</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
