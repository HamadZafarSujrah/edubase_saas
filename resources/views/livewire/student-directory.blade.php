<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-4 shadow-sm">
        <div>
            <h2 class="h4 mb-0 fw-bold"><i class="fas fa-users-viewfinder me-2 text-primary"></i> Student Management Directory</h2>
            <p class="text-muted small mb-0">Search, filter, and manage all enrolled students across campuses.</p>
        </div>
        <div class="d-flex gap-2">
            <!-- Column Visibility Dropdown (Matching your screenshot suggestion) -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-columns me-1"></i> Columns
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg p-3" style="min-width: 250px;">
                    <li><h6 class="dropdown-header px-0 text-dark fw-bold">Show/Hide Columns</h6></li>
                    @foreach($showColumns as $key => $visible)
                        <li class="mb-1">
                            <div class="form-check form-switch px-4 py-1">
                                <input class="form-check-input" type="checkbox" role="switch" id="col_{{ $key }}" 
                                       wire:click="toggleColumn('{{ $key }}')" @if($visible) checked @endif>
                                <label class="form-check-label small fw-semibold text-muted text-capitalize" for="col_{{ $key }}">
                                    {{ str_replace('_', ' ', $key) }}
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <button wire:click="exportExcel" class="btn btn-outline-success shadow-sm">
                <i class="fas fa-file-excel me-1"></i> Export Excel
            </button>
            <a href="/student-admission" class="btn btn-primary shadow-sm"><i class="fas fa-user-plus me-1"></i> New Admission</a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control bg-light border-0" placeholder="Name, Admission No, Father Name...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Campus</label>
                    <select wire:model.live="campus_id" class="form-select bg-light border-0">
                        <option value="">All Campuses</option>
                        @foreach($campuses as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold">Class</label>
                    <select wire:model.live="school_class_id" class="form-select bg-light border-0" {{ !$campus_id ? 'disabled' : '' }}>
                        <option value="">All Classes</option>
                        @foreach($classes as $cls) <option value="{{ $cls->id }}">{{ $cls->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold form-label">Section</label>
                    <select wire:model.live="section_id" class="form-select bg-light border-0" {{ !$school_class_id ? 'disabled' : '' }}>
                        <option value="">All Sections</option>
                        @foreach($sections as $sec) <option value="{{ $sec->id }}">{{ $sec->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold form-label">Status</label>
                    <select wire:model.live="status_filter" class="form-select bg-light border-0">
                        <option value="active">Active</option>
                        <option value="inactive">Alumni / Inactive</option>
                        <option value="all">All</option>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="$reset" class="btn btn-light w-100 border fw-bold small text-muted">Clear</button>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-info border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-1"></i> {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Data Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        @if($showColumns['photo']) <th class="ps-4">Photo</th> @endif
                        @if($showColumns['admission_no']) <th>Adm No</th> @endif
                        @if($showColumns['name']) <th>Student Name</th> @endif
                        @if($showColumns['academic']) <th>Academic Info</th> @endif
                        @if($showColumns['family']) <th>Family Info</th> @endif
                        @if($showColumns['cnic']) <th>CNIC/Identity</th> @endif
                        @if($showColumns['dob']) <th>DOB/Gender</th> @endif
                        @if($showColumns['logistics']) <th>Logistics</th> @endif
                        @if($showColumns['status']) <th>Status</th> @endif
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($students as $student)
                        <tr>
                            @if($showColumns['photo'])
                            <td class="ps-4">
                                @if($student->hasMedia('profile_photos'))
                                    <img src="{{ $student->getFirstMediaUrl('profile_photos') }}" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->first_name) }}&background=E9ECEF&color=adb5bd" class="rounded-circle border" style="width: 45px; height: 45px; object-fit: cover;">
                                @endif
                            </td>
                            @endif

                            @if($showColumns['admission_no'])
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-2 py-1">{{ $student->system_id ?? 'N/A' }}</span>
                                <div class="small text-muted mt-1" style="font-size: 10px;">Adm: {{ $student->admission_no }}</div>
                            </td>
                            @endif

                            @if($showColumns['name'])
                            <td><div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</div></td>
                            @endif

                            @if($showColumns['academic'])
                            <td>
                                <div class="small fw-bold">{{ $student->schoolClass->name }} - {{ $student->section->name ?? 'No Section' }}</div>
                                <div class="small text-muted">{{ $student->campus->name }}</div>
                            </td>
                            @endif

                            @if($showColumns['family'])
                            <td>
                                <div class="small fw-bold">{{ $student->father_name }}</div>
                                <div class="small text-muted">{{ $student->mother_name ?: '-' }}</div>
                            </td>
                            @endif

                            @if($showColumns['cnic'])
                            <td>
                                <div class="small fw-bold">{{ $student->cnic_no ?: 'No CNIC' }}</div>
                                <div class="small text-muted">F: {{ $student->father_cnic }}</div>
                            </td>
                            @endif

                            @if($showColumns['dob'])
                            <td>
                                <div class="small fw-bold">{{ $student->dob ?: '-' }}</div>
                                <div class="small text-muted text-capitalize">{{ $student->gender ?: '-' }}</div>
                            </td>
                            @endif

                            @if($showColumns['logistics'])
                            <td>
                                <div class="small d-flex flex-column gap-1">
                                    <span class="badge {{ $student->transport_facility ? 'bg-success' : 'bg-light text-muted' }} bg-opacity-10 text-success rounded-pill border-0" style="font-size: 10px;">Transport: {{ $student->transport_facility ? 'Yes' : 'No' }}</span>
                                    <span class="badge {{ $student->hostel_facility ? 'bg-info' : 'bg-light text-muted' }} bg-opacity-10 text-info rounded-pill border-0" style="font-size: 10px;">Hostel: {{ $student->hostel_facility ? 'Yes' : 'No' }}</span>
                                </div>
                            </td>
                            @endif

                            @if($showColumns['status'])
                            <td>
                                @if($student->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Inactive</span>
                                @endif
                            </td>
                            @endif

                            <td class="text-end pe-4">
                                <div class="btn-group">
                                    {{-- 1. View Profile --}}
                                    <a href="{{ route('students.profile', $student->id) }}"
                                       title="View Profile"
                                       class="btn btn-outline-info btn-sm rounded-circle me-1">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- 2. Fee Plan (create or view/edit) --}}
                                    <a href="{{ route('create.fee-plan.form', ['sid' => $student->id]) }}"
                                       title="Manage Fee Plan"
                                       class="btn btn-warning btn-sm rounded-circle me-1 shadow-sm">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </a>

                                    {{-- 3. Edit Student Bio --}}
                                    <a href="{{ route('students.admission') }}?edit={{ $student->id }}"
                                       title="Edit Student Bio"
                                       class="btn btn-outline-primary btn-sm rounded-circle me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{-- 4. Print Admission Form (opens profile in new tab) --}}
                                    <a href="{{ route('students.profile', $student->id) }}"
                                       target="_blank"
                                       title="Print Admission / ID"
                                       class="btn btn-outline-dark btn-sm rounded-circle me-1">
                                        <i class="fas fa-id-card"></i>
                                    </a>

                                    {{-- 5. Delete Student --}}
                                    <button wire:click="deleteStudent({{ $student->id }})"
                                            wire:confirm="Delete this student's entire record? This cannot be undone."
                                            title="Delete Student"
                                            class="btn btn-outline-danger btn-sm rounded-circle">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="20" class="text-center py-5">
                                <i class="fas fa-user-slash fs-1 text-muted opacity-25 mb-3"></i>
                                <p class="text-muted mb-0">No students found matching your criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-4">
            {{ $students->links() }}
        </div>
    </div>
</div>
