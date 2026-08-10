<div class="container-fluid pb-5">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-user-plus me-2 text-warning"></i> Add Bulk Students</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Add Bulk Students</li>
                </ol>
            </nav>
        </div>
        <button wire:click="downloadTemplate" class="btn btn-outline-primary rounded-pill px-4 btn-sm">
            <i class="fas fa-file-download me-2"></i> Download Template
        </button>
    </div>

    @if($showing_results)
        {{-- RESULTS --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5 text-center">
                <i class="fas fa-check-circle fs-1 text-success mb-3 d-block"></i>
                <h4 class="fw-bold">{{ $imported_count }} student(s) imported successfully.</h4>

                @if(count($failed_rows) > 0)
                    <div class="alert alert-danger border-0 shadow-sm mt-4 text-start">
                        <div class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>{{ count($failed_rows) }} row(s) could not be imported:</div>
                        <ul class="mb-0 small">
                            @foreach($failed_rows as $f)
                                <li>{{ $f }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-4 d-flex gap-3 justify-content-center">
                    <button wire:click="startOver" class="btn btn-primary rounded-pill px-4"><i class="fas fa-redo me-2"></i> Import More</button>
                    <a href="{{ route('students.directory') }}" class="btn btn-outline-secondary rounded-pill px-4"><i class="fas fa-users me-2"></i> View Student Directory</a>
                </div>
            </div>
        </div>

    @elseif(count($preview_rows) > 0)
        {{-- PREVIEW --}}
        @php
            $validCount = collect($preview_rows)->where('valid', true)->count();
            $invalidCount = count($preview_rows) - $validCount;
        @endphp

        @if(count($unrecognized_headers) > 0)
            <div class="alert alert-warning border-0 shadow-sm mb-3">
                <i class="fas fa-info-circle me-2"></i> These columns weren't recognized and were ignored: <strong>{{ implode(', ', $unrecognized_headers) }}</strong>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                <span class="text-white fw-bold"><i class="fas fa-list-check me-2"></i> Preview — {{ count($preview_rows) }} row(s)</span>
                <div>
                    <span class="badge bg-success px-3 py-2 me-1">{{ $validCount }} ready</span>
                    @if($invalidCount > 0)
                        <span class="badge bg-danger px-3 py-2">{{ $invalidCount }} with errors</span>
                    @endif
                </div>
            </div>
            <div class="table-responsive" style="max-height: 500px;">
                <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.78rem;">
                    <thead>
                        <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                            <th class="text-center ps-3" width="50">Row</th>
                            <th>Name</th>
                            <th>Father Name</th>
                            <th>Campus</th>
                            <th>Class / Section</th>
                            <th>Status</th>
                            <th class="text-center pe-3" width="60"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview_rows as $i => $row)
                            <tr class="{{ $row['valid'] ? '' : 'table-danger' }}">
                                <td class="text-center ps-3 fw-bold text-muted">{{ $row['row'] }}</td>
                                <td class="fw-bold">{{ $row['data']['first_name'] }} {{ $row['data']['last_name'] }}</td>
                                <td>{{ $row['data']['father_name'] }}</td>
                                <td>{{ $row['data']['campus_name'] }}</td>
                                <td>{{ $row['data']['class_name'] }} {{ $row['data']['section_name'] ? '/ ' . $row['data']['section_name'] : '' }}</td>
                                <td>
                                    @if($row['valid'])
                                        <span class="badge bg-success-subtle text-success"><i class="fas fa-check me-1"></i>Ready</span>
                                    @else
                                        <span class="text-danger small">{{ implode('; ', $row['errors']) }}</span>
                                    @endif
                                </td>
                                <td class="text-center pe-3">
                                    <button wire:click="removeRow({{ $i }})" class="btn btn-link text-danger p-0"><i class="fas fa-times"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button wire:click="confirmImport" wire:loading.attr="disabled" @if($validCount === 0) disabled @endif class="btn btn-success px-5 py-2 fw-bold rounded-3 shadow-sm">
                <i class="fas fa-check-circle me-2"></i> Import {{ $validCount }} Valid Student(s)
            </button>
            <button wire:click="startOver" class="btn btn-outline-secondary px-4 rounded-3">
                <i class="fas fa-times me-2"></i> Cancel
            </button>
        </div>

    @else
        {{-- UPLOAD --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-5 text-center">
                <i class="fas fa-file-csv fs-1 text-warning mb-3 d-block"></i>
                <h5 class="fw-bold mb-2">Upload a CSV or Excel file</h5>
                <p class="text-muted mb-4">
                    Required columns: <strong>First Name, Father Name, Campus, Class</strong>.<br>
                    Optional: Last Name, Father CNIC, Father Phone, CNIC, Gender, Date of Birth, Religion, Nationality, Section, Address, City.
                </p>

                <div class="mx-auto" style="max-width: 420px;">
                    <input type="file" wire:model="file" class="form-control border-0 bg-light shadow-sm" accept=".csv,.xlsx,.xls">
                    <div wire:loading wire:target="file" class="small text-primary fw-bold mt-2">
                        <span class="spinner-border spinner-border-sm me-2"></span> Parsing file...
                    </div>
                    @error('file') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
    @endif
</div>
