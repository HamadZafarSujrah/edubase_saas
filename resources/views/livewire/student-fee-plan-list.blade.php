<div class="container-fluid py-4 min-vh-100 bg-light">
    <!-- Page Header & Global Actions -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="bg-primary py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-white"><i class="fas fa-file-invoice-dollar me-2"></i> View/Edit Student Fee Plans</h5>
            <div class="d-flex gap-2">
                <a href="/generate-challans" class="btn btn-warning btn-sm fw-bold px-3"><i class="fas fa-magic me-1"></i> Generate Monthly Bills</a>
            </div>
        </div>
        <div class="card-body bg-white p-4">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0 shadow-none" placeholder="Search by Name, Reg No, or Father's Name...">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filter Class</label>
                    <select wire:model.live="class_id" class="form-select bg-light border-0 shadow-none">
                        <option value="">All Classes</option>
                        @foreach($classes as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Section</label>
                    <select wire:model.live="section_id" class="form-select bg-light border-0 shadow-none" {{ !$class_id ? 'disabled' : '' }}>
                        <option value="">All Sections</option>
                        @foreach($sections as $s) <option value="{{ $s->id }}">{{ $s->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="$refresh" class="btn btn-primary w-100 fw-bold py-2"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Matrix Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary bg-opacity-10">
                    <tr class="tiny fw-bold text-uppercase text-primary shadow-sm">
                        <th class="ps-4 py-3">Reg No</th>
                        <th>Student Name</th>
                        <th>Father Name</th>
                        <th>Class & Section</th>
                        <th>Current Assigned Plan</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse($students as $student)
                        <tr class="transition-hover">
                            <td class="ps-4">
                                <span class="badge bg-light text-dark fw-bold border">{{ $student->admission_no }}</span>
                            </td>
                            <td>
                                <div class="fw-bold fs-6">{{ $student->first_name }} {{ $student->last_name }}</div>
                                <div class="tiny text-muted">Joined: {{ $student->admission_date ? date('M Y', strtotime($student->admission_date)) : 'N/A' }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold">{{ $student->father_name }}</div>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ $student->schoolClass->name ?? 'N/A' }}</div>
                                <div class="tiny text-muted">{{ $student->section->name ?? '' }}</div>
                            </td>
                            <td>
                                @if($student->feePlan)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 fw-bold">
                                        {{ $student->feePlan->name }}
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 fw-bold">
                                        No Plan Assigned
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <a href="/student-fee-plan/{{ $student->id }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="fas fa-edit me-1"></i> Edit Plan
                                </a>
                                <a href="/student-profile/{{ $student->id }}" class="btn btn-light btn-sm rounded-pill px-3 border ms-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-users-slash fs-1 text-muted opacity-25 mb-3 d-block"></i>
                                    <p class="text-muted">No students found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $students->links() }}
        </div>
    </div>
</div>

<style>
    .transition-hover:hover { background-color: #f8fafc !important; }
    .tiny { font-size: 0.75rem; }
    .page-link { border-radius: 8px !important; margin: 0 2px; }
</style>
