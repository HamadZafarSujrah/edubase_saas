<div class="container-fluid py-4 bg-light min-vh-100">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border-start border-5 border-primary">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Institution Management Dashboard</h1>
            <p class="text-muted mb-0 small"><i class="fas fa-calendar-alt me-2"></i>{{ now()->format('l, jS F Y') }}</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <p class="fw-bold mb-0 text-dark">{{ auth()->user()->name }}</p>
                <span class="badge bg-soft-primary text-primary px-3 rounded-pill">{{ auth()->user()->role ?? 'Administrator' }}</span>
            </div>
            <div class="bg-primary text-white p-3 rounded-circle shadow-sm">
                <i class="fas fa-user-shield fs-4"></i>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-4 mb-4">
        <!-- Students Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-gradient-primary text-white overflow-hidden position-relative">
                <div class="card-body p-4 position-relative z-1">
                    <p class="text-white text-opacity-75 small fw-bold mb-1 uppercase letter-spacing-1">Active Students</p>
                    <h2 class="display-6 fw-bold mb-0">{{ number_format($totalActiveStudents) }}</h2>
                    <div class="mt-3 small text-white text-opacity-75">
                        <i class="fas fa-user-graduate me-1"></i> Currently enrolled
                    </div>
                </div>
                <i class="fas fa-user-graduate position-absolute end-0 bottom-0 p-3 fs-1 opacity-25"></i>
            </div>
        </div>

        <!-- Financial Status Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative border-bottom border-4 border-success">
                <div class="card-body p-4">
                    <p class="text-muted small fw-bold mb-1 uppercase letter-spacing-1">This Month's Collection</p>
                    <h2 class="display-6 fw-bold mb-0 text-dark">{{ $monthlyCollected >= 1000 ? number_format($monthlyCollected / 1000, 0) . 'k' : number_format($monthlyCollected) }}</h2>
                    <div class="mt-3 small {{ $collectionRate === null ? 'text-muted' : ($collectionRate >= 75 ? 'text-success' : ($collectionRate >= 50 ? 'text-warning' : 'text-danger')) }}">
                        @if($collectionRate === null)
                            <i class="fas fa-info-circle me-1"></i> No challans billed yet
                        @else
                            <i class="fas fa-check-circle me-1"></i> {{ $collectionRate }}% of billed collected
                        @endif
                    </div>
                </div>
                <i class="fas fa-money-bill-wave position-absolute end-0 bottom-0 p-3 fs-1 text-success opacity-10"></i>
            </div>
        </div>

        <!-- Today's Attendance Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative border-bottom border-4 border-warning">
                <div class="card-body p-4">
                    <p class="text-muted small fw-bold mb-1 uppercase letter-spacing-1">Today's Attendance</p>
                    <h2 class="display-6 fw-bold mb-0 text-dark">{{ $attendanceRate === null ? '—' : $attendanceRate . '%' }}</h2>
                    <div class="mt-3 small {{ $attendanceRate === null ? 'text-muted' : 'text-warning' }}">
                        @if($attendanceRate === null)
                            <i class="fas fa-exclamation-circle me-1"></i> Not yet taken today
                        @else
                            <i class="fas fa-check-circle me-1"></i> {{ number_format($todayMarked) }} student(s) marked
                        @endif
                    </div>
                </div>
                <i class="fas fa-school position-absolute end-0 bottom-0 p-3 fs-1 text-warning opacity-10"></i>
            </div>
        </div>

        <!-- Staff Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden position-relative border-bottom border-4 border-info">
                <div class="card-body p-4">
                    <p class="text-muted small fw-bold mb-1 uppercase letter-spacing-1">Active Employees</p>
                    <h2 class="display-6 fw-bold mb-0 text-dark">{{ number_format($totalActiveEmployees) }}</h2>
                    <div class="mt-3 small text-info">
                        Active Personnel
                    </div>
                </div>
                <i class="fas fa-users-cog position-absolute end-0 bottom-0 p-3 fs-1 text-info opacity-10"></i>
            </div>
        </div>
    </div>

    <!-- Second Row: Quick Actions & Pending Approvals -->
    <div class="row g-4">
        <!-- Quick Actions Grid -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-header bg-white border-0 p-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0">Operational Quick Links</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <a href="{{ route('finance.journal-entry') }}" class="d-block text-decoration-none group p-4 border rounded-4 text-center bg-light-hover transition-all shadow-sm-hover border-light border-2">
                                <div class="bg-soft-success text-success p-3 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-book-open fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Journal Entry</h6>
                                <p class="small text-muted mb-0">Post financial vouchers</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('students.admission') }}" class="d-block text-decoration-none group p-4 border rounded-4 text-center bg-light-hover transition-all shadow-sm-hover border-light border-2">
                                <div class="bg-soft-primary text-primary p-3 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-user-plus fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">New Admission</h6>
                                <p class="small text-muted mb-0">Register new student</p>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="{{ route('attendance.take') }}" class="d-block text-decoration-none group p-4 border rounded-4 text-center bg-light-hover transition-all shadow-sm-hover border-light border-2">
                                <div class="bg-soft-warning text-warning p-3 rounded-circle d-inline-block mb-3">
                                    <i class="fas fa-hand-paper fs-4"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Take Attendance</h6>
                                <p class="small text-muted mb-0">Mark today's attendance</p>
                            </a>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="my-4 border-bottom opacity-10"></div>

                    <!-- Fee Collection Trend Chart -->
                    <div class="viz-root">
                        <h6 class="text-muted fw-bold mb-3">Fee Collection Trend — Last 6 Months</h6>
                        @php
                            $chartW = 640; $barAreaH = 120; $baselineY = 150;
                            $maxAmount = max(1, max(array_column($collectionTrend, 'amount')));
                            $n = max(1, count($collectionTrend));
                            $slot = $chartW / $n;
                            $barW = min(56, $slot - 16);
                        @endphp
                        @if($maxAmount <= 1)
                            <div class="text-center py-4 text-muted small">No fee collection recorded in the last 6 months yet.</div>
                        @else
                            <svg viewBox="0 0 {{ $chartW }} 190" class="w-100" style="max-height: 220px;" role="img" aria-label="Fee collection trend, last 6 months">
                                <line x1="0" y1="{{ $baselineY }}" x2="{{ $chartW }}" y2="{{ $baselineY }}" stroke="#c3c2b7" stroke-width="1" />
                                @foreach($collectionTrend as $i => $point)
                                    @php
                                        $barH = max(($point['amount'] / $maxAmount) * $barAreaH, $point['amount'] > 0 ? 3 : 0);
                                        $x = $i * $slot + ($slot - $barW) / 2;
                                        $y = $baselineY - $barH;
                                    @endphp
                                    <g>
                                        <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barW }}" height="{{ $barH }}" rx="4" fill="#2a78d6">
                                            <title>{{ $point['label'] }}: PKR {{ number_format($point['amount'], 0) }}</title>
                                        </rect>
                                        @if($point['amount'] > 0)
                                            <text x="{{ $x + $barW / 2 }}" y="{{ $y - 8 }}" text-anchor="middle" font-size="11" fill="#52514e">{{ $point['amount'] >= 1000 ? number_format($point['amount'] / 1000, 0) . 'k' : number_format($point['amount']) }}</text>
                                        @endif
                                        <text x="{{ $x + $barW / 2 }}" y="{{ $baselineY + 18 }}" text-anchor="middle" font-size="11" fill="#898781">{{ $point['label'] }}</text>
                                    </g>
                                @endforeach
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals & Alerts -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden">
                <div class="card-header bg-dark text-white p-4 border-0">
                    <h5 class="fw-bold mb-0"><i class="fas fa-bell me-2"></i>Pending Approvals</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('hrm.leave-approvals') }}" class="list-group-item p-4 border-0 border-bottom d-flex justify-content-between align-items-center text-decoration-none">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-calendar-minus me-2 text-warning"></i>Leave Requests</h6>
                                <p class="mb-0 small text-muted">Awaiting your decision</p>
                            </div>
                            <span class="badge {{ $pendingLeaveRequests > 0 ? 'bg-warning' : 'bg-secondary' }} rounded-pill px-3 py-2 fs-6">{{ $pendingLeaveRequests }}</span>
                        </a>
                        <a href="{{ route('hrm.salary-plan-approvals') }}" class="list-group-item p-4 border-0 border-bottom d-flex justify-content-between align-items-center text-decoration-none">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-file-signature me-2 text-warning"></i>Salary Plans</h6>
                                <p class="mb-0 small text-muted">Awaiting approval</p>
                            </div>
                            <span class="badge {{ $pendingSalaryApprovals > 0 ? 'bg-warning' : 'bg-secondary' }} rounded-pill px-3 py-2 fs-6">{{ $pendingSalaryApprovals }}</span>
                        </a>
                        <a href="{{ route('general.complaints') }}" class="list-group-item p-4 border-0 border-bottom d-flex justify-content-between align-items-center text-decoration-none">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark"><i class="fas fa-exclamation-circle me-2 text-danger"></i>Open Complaints</h6>
                                <p class="mb-0 small text-muted">Open or in progress</p>
                            </div>
                            <span class="badge {{ $openComplaints > 0 ? 'bg-danger' : 'bg-secondary' }} rounded-pill px-3 py-2 fs-6">{{ $openComplaints }}</span>
                        </a>
                        <div class="list-group-item p-4 border-0">
                            <div class="text-center">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-4" disabled>All caught up when zero</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-gradient-primary { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); }
        .bg-soft-primary { background-color: rgba(78, 115, 223, 0.1); }
        .bg-soft-success { background-color: rgba(28, 200, 138, 0.1); }
        .bg-soft-warning { background-color: rgba(246, 194, 62, 0.1); }
        .bg-light-hover:hover { background-color: rgba(0,0,0,0.02) !important; }
        .transition-all { transition: all 0.3s ease; }
        .shadow-sm-hover:hover { shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important; }
        .uppercase { text-transform: uppercase; }
        .letter-spacing-1 { letter-spacing: 1px; }
        .x-small { font-size: 0.75rem; }
        .bg-success-soft { background-color: #d1e7dd; }
    </style>
</div>
