<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-exclamation-circle me-2 text-primary"></i> Complaints</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Complaints</li>
                </ol>
            </nav>
        </div>
        <button wire:click="openForm" class="btn btn-primary rounded-pill px-4 btn-sm"><i class="fas fa-plus me-2"></i> Log New Complaint</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <label class="small fw-bold text-muted mb-1">Status</label>
            <select wire:model.live="status_filter" class="form-select border-0 bg-light shadow-sm" style="max-width: 250px;">
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="all">All</option>
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-history me-2"></i> Complaints</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $complaints->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Subject</th>
                        <th>Complainant</th>
                        <th>Regarding</th>
                        <th class="text-center" width="100">Status</th>
                        <th width="150">Assigned To</th>
                        <th class="text-center pe-3" width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $c->subject }}<div class="text-muted small text-truncate" style="max-width:250px;" title="{{ $c->description }}">{{ $c->description }}</div></td>
                            <td class="text-muted">{{ $c->complainant_name ?: '—' }} {{ $c->complainant_phone ? '('.$c->complainant_phone.')' : '' }}</td>
                            <td class="text-muted">{{ $c->student ? $c->student->first_name . ' ' . $c->student->last_name : '—' }}</td>
                            <td class="text-center">
                                <span class="badge {{ $c->status === 'resolved' ? 'bg-success' : ($c->status === 'in_progress' ? 'bg-info' : 'bg-warning') }} text-uppercase">{{ str_replace('_',' ', $c->status) }}</span>
                            </td>
                            <td class="text-muted">{{ $c->assignedTo->name ?? '—' }}</td>
                            <td class="text-center pe-3">
                                @if($c->status === 'open')
                                    <button wire:click="markInProgress({{ $c->id }})" class="btn btn-sm btn-info rounded-pill px-3 mb-1"><i class="fas fa-tasks"></i> In Progress</button>
                                @endif
                                @if($c->status !== 'resolved')
                                    <button wire:click="resolve({{ $c->id }})" wire:confirm="Mark this complaint as resolved?" class="btn btn-sm btn-success rounded-pill px-3"><i class="fas fa-check"></i> Resolve</button>
                                @else
                                    <span class="text-muted small">{{ $c->resolved_at?->format('d-M-Y') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-check-circle fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No complaints found for the selected filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $complaints->links() }}
        </div>
    </div>

    @if($isFormOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-circle text-primary me-2"></i>Log New Complaint</h5>
            <button type="button" class="btn-close" wire:click="closeForm"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light border-0" wire:model="subject">
                  @error('subject') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="mb-3">
                  <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                  <textarea rows="4" class="form-control bg-light border-0" wire:model="description"></textarea>
                  @error('description') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="row">
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Complainant Name</label>
                      <input type="text" class="form-control bg-light border-0" wire:model="complainant_name">
                  </div>
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Complainant Phone</label>
                      <input type="text" class="form-control bg-light border-0" wire:model="complainant_phone">
                  </div>
              </div>
              <div class="position-relative">
                  <label class="form-label fw-bold">Regarding Student (Optional)</label>
                  <input type="text" wire:model.live.debounce.300ms="student_search" placeholder="Search by name or admission no...." class="form-control bg-light border-0" autocomplete="off">
                  @if(count($suggested_students) > 0)
                      <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000;">
                          @foreach($suggested_students as $s)
                              <button type="button" wire:click="selectStudent({{ $s['id'] }})" class="list-group-item list-group-item-action">
                                  <strong>{{ $s['first_name'] }} {{ $s['last_name'] }}</strong>
                                  <span class="text-muted small"> — {{ $s['admission_no'] }}</span>
                              </button>
                          @endforeach
                      </div>
                  @endif
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeForm">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="submit">Submit Complaint</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
