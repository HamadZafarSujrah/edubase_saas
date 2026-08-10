<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fas fa-comment-dots me-2 text-primary"></i> SMS Templates</h2>
        <button wire:click="openModal" class="btn btn-primary shadow-sm"><i class="fas fa-plus me-1"></i> Add New Template</button>
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
                            <th>Category</th>
                            <th>Body</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">{{ $template->name }}</td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $template->category }}</span></td>
                            <td class="text-muted text-truncate" style="max-width: 350px;" title="{{ $template->body }}">{{ $template->body }}</td>
                            <td class="text-end pe-4">
                                <button wire:click="edit({{ $template->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $template->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-comment-dots mb-3" style="font-size: 3rem; color: #ddd;"></i>
                                <h5>No Templates Found</h5>
                                <p>E.g. "Fee Reminder", "Exam Result".</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 mt-2">
            {{ $templates->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $template_id ? 'Edit Template' : 'Create Template' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="row">
                  <div class="col-8 mb-3">
                      <label class="form-label fw-bold">Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control bg-light" wire:model="name" placeholder="E.g. Fee Reminder">
                      @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-4 mb-3">
                      <label class="form-label fw-bold">Category</label>
                      <select class="form-select bg-light" wire:model="category">
                          <option value="general">General</option>
                          <option value="fee">Fee</option>
                          <option value="attendance">Attendance</option>
                          <option value="result">Result</option>
                          <option value="hr">HR</option>
                      </select>
                  </div>
              </div>
              <div class="mb-2">
                  <label class="form-label fw-bold">Message Body <span class="text-danger">*</span></label>
                  <textarea rows="4" maxlength="640" class="form-control bg-light" wire:model="body" placeholder="Use {student_name} and {class} to personalize..."></textarea>
                  @error('body') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="small text-muted">Placeholders: <code>{student_name}</code>, <code>{class}</code></div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save Template</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
