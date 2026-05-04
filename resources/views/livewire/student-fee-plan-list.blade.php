<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-{{ $mode === 'create' ? 'file-invoice-dollar' : 'edit' }} me-2 text-primary"></i> {{ $mode === 'create' ? 'Create Fee Plans' : 'View / Edit Fee Plans' }}</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">{{ $mode === 'create' ? 'Create Fee Plans' : 'View / Edit Fee Plans' }}</li>
                </ol>
            </nav>
        </div>
        <div class="text-end">
            <span class="badge bg-primary fs-6 px-4 py-2 rounded-pill shadow-sm">
                Showing — {{ $students->total() }} items
            </span>
        </div>
    </div>



    {{-- Filters Row --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Search Student / Father / SID</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input wire:model.live.debounce.300ms="search" type="text"
                               class="form-control border-start-0 shadow-none"
                               placeholder="Name, Admission No, Father Name...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Class</label>
                    <select wire:model.live="class_id" class="form-select bg-light border-0 shadow-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Section</label>
                    <select wire:model.live="section_id" class="form-select bg-light border-0 shadow-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Fee Plan Filter</label>
                    <input wire:model.live.debounce.300ms="fee_plan_search" type="text"
                           class="form-control bg-light border-0 shadow-sm"
                           placeholder="Search by fee plan name...">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button wire:click="$set('search',''); $set('class_id',''); $set('section_id',''); $set('fee_plan_search','')"
                            class="btn btn-outline-secondary w-100 rounded-3">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Students Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white py-3 border-0">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i> Enrolled Students — Fee Plan Status</h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-sm rounded-3 text-success fw-bold" onclick="exportTable()">
                        <i class="fas fa-file-excel me-1"></i> Excel
                    </button>
                    <button class="btn btn-light btn-sm rounded-3 text-danger fw-bold" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="feePlanTable">
                <thead class="bg-dark text-white">
                    <tr class="text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        <th class="ps-3" width="40"><input type="checkbox" class="form-check-input"></th>
                        <th width="50">NO.</th>
                        <th>SID</th>
                        <th>STUDENT NAME</th>
                        <th>FATHER / GUARDIAN</th>
                        <th>CLASS</th>
                        <th>SECTION</th>
                        <th>ASSIGNED FEE PLAN</th>
                        <th>W.E.F</th>
                        <th class="text-end">TUITION FEE</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                    {{-- Inline filter row --}}
                    <tr class="bg-warning bg-opacity-10" style="font-size: 0.75rem;">
                        <td></td>
                        <td></td>
                        <td><input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm border-0 bg-light" placeholder="SID..."></td>
                        <td><input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm border-0 bg-light" placeholder="Name..."></td>
                        <td><input wire:model.live.debounce.300ms="search" type="text" class="form-control form-control-sm border-0 bg-light" placeholder="Father..."></td>
                        <td>
                            <select wire:model.live="class_id" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Class</option>
                                @foreach($classes as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select wire:model.live="section_id" class="form-select form-select-sm border-0 bg-light">
                                <option value="">Section</option>
                                @foreach($sections as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input wire:model.live.debounce.300ms="fee_plan_search" type="text" class="form-control form-control-sm border-0 bg-light" placeholder="Plan..."></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr class="{{ $student->feePlanItems()->count() > 0 ? '' : 'table-warning' }}">
                            <td class="ps-3">
                                <input type="checkbox" class="form-check-input">
                            </td>
                            <td class="text-muted small fw-bold">{{ $students->firstItem() + $index }}</td>
                            <td>
                                <span class="fw-bold text-primary">{{ $student->admission_no }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</div>
                                <div class="tiny text-muted">{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d-M-Y') : '' }}</div>
                            </td>
                            <td>
                                <div class="small text-dark">{{ $student->father_name }}</div>
                                <div class="tiny text-muted">{{ $student->father_phone }}</div>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark fw-bold">{{ $student->schoolClass->name ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary fw-bold">{{ $student->section->name ?? '—' }}</span>
                            </td>
                            <td>
                                @if($student->feePlan)
                                    <span class="text-success fw-bold small">
                                        <i class="fas fa-check-circle me-1"></i>{{ $student->feePlan->name }}
                                    </span>
                                @else
                                    <span class="text-muted small fst-italic">No Plan Assigned</span>
                                @endif
                            </td>
                            <td>
                                @if($student->fee_plan_effect_from)
                                    <span class="small fw-bold text-dark">{{ \Carbon\Carbon::parse($student->fee_plan_effect_from)->format('M Y') }}</span>
                                @elseif($student->feePlanItems->count() > 0)
                                    <span class="small fw-bold text-dark">{{ $student->feePlanItems->first()->created_at->format('M Y') }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @php
                                    $tuitionItem = $student->feePlanItems->first(function($item) {
                                        return str_contains(strtolower($item->particular->name ?? ''), 'tuition');
                                    });
                                @endphp
                                @if($tuitionItem)
                                    @php
                                        $finalFee = $tuitionItem->actual_amount - $tuitionItem->discount_amount;
                                    @endphp
                                    <span class="fw-bold text-dark">{{ number_format($finalFee) }}</span>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($mode === 'edit')
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('create.fee-plan.form', ['sid' => $student->id]) }}"
                                           class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <button wire:click="deleteFeePlan({{ $student->id }})"
                                                wire:confirm="Are you sure you want to delete this student's fee plan completely? They will be moved back to the pending creation list."
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
                                            <i class="fas fa-trash-alt me-1"></i> Delete
                                        </button>
                                    </div>
                                @elseif($mode === 'view')
                                    <a href="{{ route('create.fee-plan.form', ['sid' => $student->id, 'view' => 1]) }}"
                                       class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                        <i class="fas fa-eye me-1"></i> View
                                    </a>
                                @else
                                    <a href="{{ route('create.fee-plan.form', ['sid' => $student->id]) }}"
                                       class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                        <i class="fas fa-plus me-1"></i> Create
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                                    <p class="fw-bold">No enrolled students found matching your filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
            <div class="small text-muted">
                Showing <strong>{{ $students->firstItem() ?? 0 }}</strong>–<strong>{{ $students->lastItem() ?? 0 }}</strong>
                of <strong>{{ $students->total() }}</strong> students
                @php
                    $withPlan = $students->filter(fn($s) => $s->feePlanItems()->count() > 0)->count();
                    $withoutPlan = $students->filter(fn($s) => $s->feePlanItems()->count() == 0)->count();
                @endphp
                &nbsp;|&nbsp;
                <span class="text-success fw-bold">{{ $withPlan }} with plan</span> &nbsp;|&nbsp;
                <span class="text-warning fw-bold">{{ $withoutPlan }} pending</span>
            </div>
            {{ $students->links() }}
        </div>
    </div>
    
    <style>
        .tiny { font-size: 0.72rem; }
    </style>
</div>
