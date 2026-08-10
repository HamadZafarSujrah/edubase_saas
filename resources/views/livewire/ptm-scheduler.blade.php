<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-users-cog me-2 text-primary"></i> Parent-Teacher Meetings</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Parent-Teacher Meetings</li>
                </ol>
            </nav>
        </div>
        <button wire:click="openModal" class="btn btn-primary rounded-pill px-4 btn-sm"><i class="fas fa-plus me-2"></i> Schedule Meeting</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-calendar-alt me-2"></i> Scheduled Meetings</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $meetings->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.82rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Title</th>
                        <th>Date &amp; Time</th>
                        <th>Campus</th>
                        <th>Class</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($meetings as $m)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $m->title }}</td>
                            <td class="text-muted">
                                {{ $m->meeting_date->format('d-M-Y') }}
                                @if($m->start_time) {{ \Carbon\Carbon::parse($m->start_time)->format('h:i A') }}@if($m->end_time) - {{ \Carbon\Carbon::parse($m->end_time)->format('h:i A') }}@endif @endif
                            </td>
                            <td>{{ $m->campus->name ?? 'All Campuses' }}</td>
                            <td>{{ $m->schoolClass->name ?? 'All Classes' }}</td>
                            <td class="text-end pe-3">
                                <button wire:click="edit({{ $m->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $m->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-calendar-times fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                <p class="text-muted mb-0">No meetings scheduled yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $meetings->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $ptm_id ? 'Edit Meeting' : 'Schedule New Meeting' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light border-0" wire:model="title">
                  @error('title') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="row">
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Campus (Optional)</label>
                      <select class="form-select bg-light border-0" wire:model="campus_id">
                          <option value="">All Campuses</option>
                          @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                      </select>
                  </div>
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Class (Optional)</label>
                      <select class="form-select bg-light border-0" wire:model="school_class_id">
                          <option value="">All Classes</option>
                          @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                      </select>
                  </div>
              </div>
              <div class="row">
                  <div class="col-4 mb-3">
                      <label class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                      <input type="date" class="form-control bg-light border-0" wire:model="meeting_date">
                      @error('meeting_date') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
                  <div class="col-4 mb-3">
                      <label class="form-label fw-bold">Start Time</label>
                      <input type="time" class="form-control bg-light border-0" wire:model="start_time">
                  </div>
                  <div class="col-4 mb-3">
                      <label class="form-label fw-bold">End Time</label>
                      <input type="time" class="form-control bg-light border-0" wire:model="end_time">
                  </div>
              </div>
              <div class="mb-3">
                  <label class="form-label fw-bold">Description</label>
                  <textarea rows="3" class="form-control bg-light border-0" wire:model="description"></textarea>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save Meeting</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
