<div class="container-fluid min-vh-100">
    <!-- Header Summary Section -->
    <div class="row g-3 mb-4 card shadow-sm border-0 rounded-4 overflow-hidden mx-0 bg-white shadow-none border-bottom">
        <div class="col-md-9 p-4">
            <h4 class="fw-bold mb-1"><i class="fas fa-magic text-primary me-2"></i> Monthly Bulk Billing Engine</h4>
            <p class="text-muted small mb-0">Select your criteria below to preview and generate student fees for the current billing cycle.</p>
        </div>
        <div class="col-md-3 bg-primary bg-opacity-10 p-4 border-start d-flex align-items-center justify-content-center">
            <div class="text-center">
                <h6 class="tiny fw-bold text-uppercase text-primary mb-1">Target Billing</h6>
                <h3 class="fw-bold mb-0">{{ count($this->filteredStudents) }}</h3>
                <p class="tiny fw-bold text-muted mb-0">STUDENTS FOUND</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT: Filters -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm border-0 rounded-4 p-4 sticky-top shadow-lg" style="top: 130px; z-index: 10;">
                <h6 class="fw-bold mb-4 text-uppercase small text-muted border-bottom pb-2">Generation Criteria</h6>
                
                <div class="mb-3">
                    <label class="form-label small fw-bold">Campus</label>
                    <select wire:model.live="campus_id" class="form-select bg-light border-0 shadow-sm">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Target Class</label>
                        <select wire:model.live="school_class_id" class="form-select bg-light border-0 shadow-sm">
                            <option value="">All Classes</option>
                            @foreach($classes as $class) <option value="{{ $class->id }}">{{ $class->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Section</label>
                        <select wire:model.live="section_id" class="form-select bg-light border-0 shadow-sm" {{ !$school_class_id ? 'disabled' : '' }}>
                            <option value="">All Sections</option>
                            @foreach($sections as $sec) <option value="{{ $sec->id }}">{{ $sec->name }}</option> @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                <div class="row g-2 mb-4">
                    <div class="col-7">
                        <label class="form-label small fw-bold">Billing Month</label>
                        <select wire:model="month" class="form-select bg-light border-0 shadow-sm text-capitalize">
                            @foreach($months as $m) <option value="{{ $m }}">{{ $m }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="form-label small fw-bold">Year</label>
                        <input type="number" wire:model="year" class="form-control bg-light border-0 shadow-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold">Due Date <span class="text-danger">*</span></label>
                    <input type="date" wire:model="due_date" class="form-control bg-light border-0 shadow-sm">
                </div>

                <button wire:click="generateBulk" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm mb-2" wire:loading.attr="disabled">
                    <i class="fas fa-bolt me-2"></i> GENERATE BULK BILLS
                </button>

                <a href="{{ route('print-challans', ['month' => $month, 'year' => $year, 'class_id' => $school_class_id, 'section_id' => $section_id, 'campus_id' => $campus_id]) }}" 
                   target="_blank" 
                   class="btn btn-dark w-100 py-2 fw-bold rounded-3 border-secondary">
                    <i class="fas fa-print me-2"></i> PRINT GENERATED BILLS
                </a>

                @if (session()->has('message'))
                    <div class="alert alert-success border-0 shadow-sm mt-3 animate__animated animate__fadeIn">
                        <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- RIGHT: Student Preview -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2 text-primary"></i> Billing Preview (Current Filter)</h6>
                    <span class="badge bg-light text-primary border px-3">Total: {{ count($this->filteredStudents) }} Students</span>
                </div>
                <div class="table-responsive" style="max-height: 500px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light sticky-top">
                            <tr class="tiny fw-bold text-uppercase text-muted border-bottom">
                                <th class="ps-4">Reg No</th>
                                <th>Student Name</th>
                                <th>Campus / Class</th>
                                <th class="text-end pe-4">Base Fee Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->filteredStudents as $student)
                                <tr>
                                    <td class="ps-4 small text-muted">{{ $student->admission_no }}</td>
                                    <td>
                                        <div class="fw-bold small">{{ $student->first_name }} {{ $student->last_name }}</div>
                                        <div class="tiny text-muted">Father: {{ $student->father_name }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-primary">{{ $student->campus->name ?? 'N/A' }}</div>
                                        <div class="tiny text-muted">{{ $student->schoolClass->name ?? '' }} - {{ $student->section->name ?? '' }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        @if($student->fee_plan_id)
                                            <span class="badge bg-success bg-opacity-10 text-success small border border-success border-opacity-25 px-3">Plan Assigned</span>
                                        @else
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="badge bg-danger bg-opacity-10 text-danger small border border-danger border-opacity-25 px-3 mb-1">Missing Plan</span>
                                                <a href="/student-fee-plan/{{ $student->id }}" class="tiny fw-bold text-primary text-decoration-none"><i class="fas fa-wrench me-1"></i> Assign Plan</a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="fas fa-search fs-2 text-muted opacity-25 mb-3 d-block"></i>
                                        <p class="text-muted">No students found matching your filters.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Billing Logic Card -->
            <div class="mt-4 card border-0 shadow-sm rounded-4 bg-primary bg-opacity-10">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-4 shadow" style="width: 50px; height: 50px;">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Intelligent Overlap Protection</h6>
                        <p class="small mb-0 text-muted">The engine automatically skips students who already have a bill generated for <strong>{{ strtoupper($month) }} {{ $year }}</strong>. No duplicate charges will ever be created.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover { transition: all 0.3s ease; }
    .transition-hover:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .tiny { font-size: 0.75rem; }
</style>
