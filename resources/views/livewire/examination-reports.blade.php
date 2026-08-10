<div class="container-fluid pb-5" x-data="{ tab: 'performance' }">

    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded-3 shadow-sm">
        <div>
            <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-book-open me-2 text-primary"></i> Examination Reports</h5>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small mt-1">
                    <li class="breadcrumb-item"><a href="/dashboard" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active">Examination Reports</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <label class="small fw-bold text-muted mb-1">Exam</label>
            <select wire:model.live="exam_id" class="form-select border-0 bg-light shadow-sm" style="max-width: 400px;">
                <option value="">Select exam...</option>
                @foreach($exams as $e) <option value="{{ $e->id }}">{{ $e->name }} — {{ $e->schoolClass->name ?? '' }}</option> @endforeach
            </select>
        </div>
    </div>

    @if($exam_id)
        <ul class="nav nav-pills mb-4">
            <li class="nav-item">
                <button class="nav-link" :class="tab === 'performance' ? 'active' : ''" @click="tab = 'performance'">Class Performance</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="tab === 'subjects' ? 'active' : ''" @click="tab = 'subjects'">Subject-Wise Analysis</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="tab === 'resultcard' ? 'active' : ''" @click="tab = 'resultcard'">Result Card</button>
            </li>
        </ul>

        <!-- Class Performance -->
        <div x-show="tab === 'performance'">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <span class="text-white fw-bold"><i class="fas fa-trophy me-2"></i> Class Performance</span>
                    <span class="badge bg-white text-dark px-3 py-2">{{ count($classPerformance) }} student(s)</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                                <th class="ps-3" width="80">Rank</th>
                                <th>Student</th>
                                <th class="text-center" width="110">Obtained</th>
                                <th class="text-center" width="110">Total</th>
                                <th class="text-center" width="110">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($classPerformance as $row)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $row['rank'] }}</td>
                                    <td>{{ $row['student']->first_name }} {{ $row['student']->last_name }} <span class="text-muted small">({{ $row['student']->admission_no }})</span></td>
                                    <td class="text-center">{{ $row['obtained'] }}</td>
                                    <td class="text-center">{{ $row['total'] }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $row['percentage'] >= 75 ? 'bg-success' : ($row['percentage'] >= 50 ? 'bg-warning' : 'bg-danger') }}">{{ $row['percentage'] }}%</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-5 text-muted">No marks recorded for this exam yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Subject-Wise Analysis -->
        <div x-show="tab === 'subjects'">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4 viz-root">
                    <h6 class="text-muted fw-bold mb-3">Subject Averages</h6>
                    @if(count($subjectAnalysis) === 0)
                        <div class="text-center py-4 text-muted small">No marks recorded for this exam yet.</div>
                    @else
                        @php
                            $chartW = 640; $barAreaH = 110; $baselineY = 140;
                            $n = max(1, count($subjectAnalysis));
                            $slot = $chartW / $n;
                            $barW = min(56, $slot - 16);
                        @endphp
                        <svg viewBox="0 0 {{ $chartW }} 175" class="w-100" style="max-height: 200px;" role="img" aria-label="Subject average percentages">
                            <line x1="0" y1="{{ $baselineY }}" x2="{{ $chartW }}" y2="{{ $baselineY }}" stroke="#c3c2b7" stroke-width="1" />
                            @foreach($subjectAnalysis as $i => $row)
                                @php
                                    $barH = max(($row['average'] / 100) * $barAreaH, 3);
                                    $x = $i * $slot + ($slot - $barW) / 2;
                                    $y = $baselineY - $barH;
                                @endphp
                                <g>
                                    <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $barH }}" rx="4" fill="#2a78d6">
                                        <title>{{ $row['subject']->name }}: {{ $row['average'] }}% average</title>
                                    </rect>
                                    <text x="{{ $x + $barW / 2 }}" y="{{ $y - 8 }}" text-anchor="middle" font-size="11" fill="#52514e">{{ $row['average'] }}%</text>
                                    <text x="{{ $x + $barW / 2 }}" y="{{ $baselineY + 18 }}" text-anchor="middle" font-size="11" fill="#898781">{{ \Illuminate\Support\Str::limit($row['subject']->name, 10) }}</text>
                                </g>
                            @endforeach
                        </svg>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header py-3" style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <span class="text-white fw-bold"><i class="fas fa-chart-bar me-2"></i> Subject Statistics</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <thead>
                            <tr style="background:#1e293b; color:#fff; font-size:0.72rem;" class="text-uppercase">
                                <th class="ps-3">Subject</th>
                                <th class="text-center">Average</th>
                                <th class="text-center">Highest</th>
                                <th class="text-center">Lowest</th>
                                <th class="text-center">Pass Rate</th>
                                <th class="text-center">Marked</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjectAnalysis as $row)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $row['subject']->name }}</td>
                                    <td class="text-center">{{ $row['average'] }}%</td>
                                    <td class="text-center text-success">{{ $row['highest'] }}%</td>
                                    <td class="text-center text-danger">{{ $row['lowest'] }}%</td>
                                    <td class="text-center">{{ $row['pass_rate'] }}%</td>
                                    <td class="text-center">{{ $row['count'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">No marks recorded for this exam yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Result Card -->
        <div x-show="tab === 'resultcard'">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="position-relative" style="max-width: 500px;">
                        <label class="small fw-bold text-muted mb-1">Search Student</label>
                        <input type="text" wire:model.live.debounce.300ms="student_search" placeholder="Name or admission no...." class="form-control border-0 bg-light shadow-sm" autocomplete="off">
                        @if(count($suggested_students) > 0)
                            <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 1000;">
                                @foreach($suggested_students as $s)
                                    <button type="button" wire:click="selectResultCardStudent({{ $s['id'] }})" class="list-group-item list-group-item-action">
                                        <strong>{{ $s['first_name'] }} {{ $s['last_name'] }}</strong>
                                        <span class="text-muted small"> — {{ $s['admission_no'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if($result_card_student_id)
                        <div class="mt-4">
                            <a href="{{ route('print-result-card', ['examId' => $exam_id, 'studentId' => $result_card_student_id]) }}" target="_blank" class="btn btn-primary fw-bold px-5 py-2 rounded-3 shadow-sm">
                                <i class="fas fa-print me-2"></i> View / Print Result Card
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <i class="fas fa-book-open fs-2 opacity-50 mb-3 d-block"></i>
            Select an exam above to view reports.
        </div>
    @endif
</div>
