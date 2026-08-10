<div class="container-fluid pb-5">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-book-reader me-2 text-primary"></i> Manage Exams</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Manage Exams</li>
                </ol>
            </nav>
        </div>
        <button wire:click="openModal" class="btn btn-primary rounded-pill px-4 btn-sm"><i class="fas fa-plus me-2"></i> Add New Exam</button>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <label class="small fw-bold text-muted mb-1">Filter by Class</label>
            <select wire:model.live="filter_school_class_id" class="form-select border-0 bg-light shadow-sm" style="max-width: 300px;">
                <option value="">All Classes</option>
                @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
            </select>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
            <span class="text-white fw-bold"><i class="fas fa-list me-2"></i> Exams</span>
            <span class="badge bg-white text-dark px-3 py-2">{{ $exams->total() }} total</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                        <th class="ps-3">Exam Name</th>
                        <th>Type</th>
                        <th>Class</th>
                        <th>Session</th>
                        <th>Date</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exams as $exam)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $exam->name }}</td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $exam->type }}</span></td>
                            <td>{{ $exam->schoolClass->name ?? '—' }}</td>
                            <td class="text-muted">{{ $exam->session->name ?? '—' }}</td>
                            <td class="text-muted">{{ $exam->exam_date?->format('d-M-Y') ?? '—' }}</td>
                            <td class="text-end pe-3">
                                <button wire:click="edit({{ $exam->id }})" class="btn btn-sm btn-light text-primary border"><i class="fas fa-edit"></i></button>
                                <button onclick="confirm('Are you sure?') || event.stopImmediatePropagation()" wire:click="delete({{ $exam->id }})" class="btn btn-sm btn-light text-danger border"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No exams found. E.g. "Mid-Term 2026" for Class 5.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $exams->links() }}
        </div>
    </div>

    @if($isModalOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.4);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-light">
            <h5 class="modal-title fw-bold"><i class="fas fa-edit text-primary me-2"></i>{{ $exam_id ? 'Edit Exam' : 'Create New Exam' }}</h5>
            <button type="button" class="btn-close" wire:click="closeModal"></button>
          </div>
          <div class="modal-body p-4">
              <div class="mb-3">
                  <label class="form-label fw-bold">Exam Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control bg-light border-0" wire:model="name" placeholder="E.g. Mid-Term 2026">
                  @error('name') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
              </div>
              <div class="row">
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                      <select class="form-select bg-light border-0" wire:model="type">
                          <option value="monthly">Monthly</option>
                          <option value="mid">Mid-Term</option>
                          <option value="final">Final</option>
                          <option value="quiz">Quiz</option>
                          <option value="assignment">Assignment</option>
                      </select>
                  </div>
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Class <span class="text-danger">*</span></label>
                      <select class="form-select bg-light border-0" wire:model="school_class_id">
                          <option value="">Select class...</option>
                          @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                      </select>
                      @error('school_class_id') <span class="text-danger small fw-bold">{{ $message }}</span>@enderror
                  </div>
              </div>
              <div class="row">
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Session (Optional)</label>
                      <select class="form-select bg-light border-0" wire:model="session_id">
                          <option value="">Select session...</option>
                          @foreach($sessions as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                      </select>
                  </div>
                  <div class="col-6 mb-3">
                      <label class="form-label fw-bold">Exam Date (Optional)</label>
                      <input type="date" class="form-control bg-light border-0" wire:model="exam_date">
                  </div>
              </div>
          </div>
          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary px-4" wire:click="save">Save Exam</button>
          </div>
        </div>
      </div>
    </div>
    @endif
</div>
