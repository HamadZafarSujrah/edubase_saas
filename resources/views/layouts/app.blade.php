<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduBase SaaS - Dashboard</title>
    
    <!-- Bootstrap CSS for layout and aesthetics -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @livewireStyles
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Top Utility Bar */
        .top-utility-bar {
            background-color: #9e1c22; /* Matches the dark red from your screenshot */
            color: white;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .top-utility-bar .nav-link {
            color: white;
            padding: 5px 20px;
            border-right: 1px solid rgba(255,255,255,0.2);
        }
        
        /* Main Navbar */
        .main-navbar {
            background-color: #1a1a1a;
            padding: 0;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .main-navbar .navbar-nav .nav-link {
            color: #f8f9fa;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 15px 20px;
            transition: all 0.2s ease;
        }
        .main-navbar .navbar-nav .nav-link:hover,
        .main-navbar .navbar-nav .nav-item.show > .nav-link {
            background-color: #dc3545; /* Red highlight on hover */
            color: white;
        }
        @if(isset($currentTenant) && $currentTenant->primary_color)
        .main-navbar .navbar-nav .nav-link:hover,
        .main-navbar .navbar-nav .nav-item.show > .nav-link {
            background-color: {{ $currentTenant->primary_color }} !important;
        }
        @endif

        /* Dropdown Styling */
        .dropdown-menu {
            border: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            margin-top: 0;
            padding: 10px 0;
        }
        .dropdown-item {
            padding: 8px 25px;
            font-size: 0.85rem;
            color: #333;
            font-weight: 500;
        }
        .dropdown-item i {
            width: 20px;
            margin-right: 8px;
            color: #0d6efd;
            text-align: center;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #0d6efd;
        }

        /* Page Content container */
        .content-container {
            padding: 30px;
            padding-top: 80px; /* Stronger clearance for double-navbar */
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Ensure Navbar stays above everything else */
        .main-navbar {
            z-index: 1050;
        }
    </style>
</head>
<body class="bg-light">

    <!-- Top Utility Bar (Management / Configuration zones) -->
    <div class="top-utility-bar d-flex justify-content-center">
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link text-center" style="width: 200px;" href="#"><i class="fas fa-list me-2"></i>Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-center" style="width: 200px;" href="#"><i class="fas fa-cog me-2"></i>Configuration</a>
            </li>
        </ul>
    </div>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg main-navbar sticky-top">
        <div class="container-fluid px-2">
            
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>

            @if(isset($currentTenant))
                <a class="navbar-brand d-flex align-items-center text-white ms-2" href="{{ route('dashboard') }}">
                    @if($currentTenant->logo_url)
                        <img src="{{ $currentTenant->logo_url }}" alt="{{ $currentTenant->name }}" style="height: 32px; margin-right: 10px;">
                    @endif
                    <span class="fw-bold small">{{ $currentTenant->name }}</span>
                </a>
            @endif

            <div class="collapse navbar-collapse" id="mainNav">
                
                <!-- Left Side: Management Modules -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active bg-danger' : '' }}" href="/dashboard"><i class="fas fa-home me-1"></i> Dashboard</a>
                    </li>

                    <!-- ADMISSIONS -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="admissionsDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-graduate me-1 text-warning"></i> Admissions
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><a class="dropdown-item" href="/student-admission"><i class="fas fa-handshake"></i> New Admission</a></li>
                            <li><a class="dropdown-item" href="/manage-students"><i class="fas fa-users-cog"></i> Manage Students</a></li>
                            <li><a class="dropdown-item" href="/create-fee-plans"><i class="fas fa-file-invoice-dollar text-primary"></i> Create Fee Plans</a></li>
                            <li><a class="dropdown-item" href="/view-edit-fee-plans"><i class="fas fa-edit text-warning"></i> View/Edit Student Fee Plans</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/generate-challans"><i class="fas fa-cog"></i> Generate Challan</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.generate-installment-challans') }}"><i class="fas fa-cog"></i> Generate Challan Installment Wise</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.pay-print-challans') }}"><i class="fas fa-plus-circle"></i> Add Amount in Generated Challan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/pay-print-challans"><i class="fas fa-print"></i> Pay or Print Fee Challans</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.direct-payment') }}"><i class="fas fa-money-bill-wave"></i> Direct Payment</a></li>
                            <li><a class="dropdown-item" href="{{ route('finance.family-payment') }}"><i class="fas fa-users"></i> Familywise Payment</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/paid-challans"><i class="fas fa-check-circle"></i> Paid Challans</a></li>
                            <li><a class="dropdown-item" href="/discount-types"><i class="fas fa-minus"></i> Challan Discounts</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-history"></i> Payment History</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-info-circle"></i> Detail Fee Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-day"></i> Day Wise Paid Challans</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-alt"></i> Class Wise Fee Particular Report</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-bullhorn"></i> Fee Defaulters Total Amount</a></li>
                        </ul>
                    </li>

                    <!-- EXTRAS -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="extrasDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-plus me-1 text-warning"></i> Extras
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><a class="dropdown-item" href="/print-fee-reminder"><i class="fas fa-print"></i> Print Fee Reminder</a></li>
                            <li><a class="dropdown-item" href="/manage-students"><i class="fas fa-user-friends"></i> All Students</a></li>
                            <li><a class="dropdown-item" href="/batch-promote-students"><i class="fas fa-exchange-alt"></i> Batch Transfer/Promote Students</a></li>
                            <li><a class="dropdown-item" href="/issue-certificate"><i class="fas fa-paper-plane"></i> Print Certificates</a></li>
                            <li><a class="dropdown-item" href="/certificates-log"><i class="fas fa-clipboard-list"></i> Certificates Logs</a></li>
                            <li><a class="dropdown-item" href="/add-bulk-students"><i class="fas fa-user-plus"></i> Add Bulk Students</a></li>
                            <li><a class="dropdown-item" href="{{ route('students.by-user') }}"><i class="fas fa-user-check"></i> Students By User</a></li>
                            <li><a class="dropdown-item" href="/student-qr-code"><i class="fas fa-qrcode"></i> Student QR Code</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.date-sheet') }}"><i class="fas fa-calendar-alt"></i> Date Sheet</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.roll-no-slips') }}"><i class="fas fa-id-card"></i> Roll No. Slip</a></li>
                            <li><a class="dropdown-item" href="/student-fee-increment"><i class="fas fa-chart-line"></i> Student Yearly Fee Increment</a></li>
                        </ul>
                    </li>

                    <!-- FINANCE -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="financeDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-invoice me-1 text-warning"></i> Finance
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-tools me-2"></i>Setup & Configuration</h6></li>
                            <li><a class="dropdown-item" href="/chart-of-accounts"><i class="fas fa-stream text-primary"></i> Chart of Accounts</a></li>
                            <li><a class="dropdown-item" href="/accounting-periods"><i class="fas fa-calendar-check text-primary"></i> Accounting Periods</a></li>
                            <li><a class="dropdown-item" href="/gl-groups"><i class="fas fa-project-diagram text-primary"></i> GL Groups</a></li>
                            <li><a class="dropdown-item" href="/gl-accounts"><i class="fas fa-list-ol text-primary"></i> GL Accounts</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-pencil-alt me-2"></i>Daily Transactions</h6></li>
                            <li><a class="dropdown-item" href="/journal-entry"><i class="fas fa-dollar-sign text-success"></i> Journal Entry</a></li>
                            <li><a class="dropdown-item" href="/add-expense"><i class="fas fa-cut text-danger"></i> Add Expense</a></li>
                            <li><a class="dropdown-item" href="/add-other-income"><i class="fas fa-hand-holding-usd text-success"></i> Add Other Income</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/journal-inquiry"><i class="fas fa-search-dollar"></i> Journal Inquiry</a></li>
                            <li><a class="dropdown-item" href="/gl-inquiry"><i class="fas fa-th"></i> GL Inquiry</a></li>
                            <li><a class="dropdown-item" href="/general-ledger"><i class="fas fa-columns"></i> General Ledger</a></li>
                            <li><a class="dropdown-item" href="/trial-balance"><i class="fas fa-exchange-alt"></i> Trial Balance</a></li>
                            <li><a class="dropdown-item" href="/profit-loss"><i class="fas fa-balance-scale"></i> P & L Statement</a></li>
                            <li><a class="dropdown-item" href="/balance-sheet"><i class="fas fa-chess-knight"></i> Balance Sheet</a></li>
                            <li><a class="dropdown-item" href="/cash-report"><i class="fas fa-chess-rook"></i> Cash Report</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="{{ route('finance.voided-transactions') }}"><i class="fas fa-times-circle"></i> Voided Transactions</a></li>
                        </ul>
                    </li>

                    <!-- EXAM & ATTENDANCE -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="examDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1 text-warning"></i> Exam & Attendance
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-book-reader me-2"></i>Examination</h6></li>
                            <li><a class="dropdown-item" href="{{ route('exam.enter-marks') }}"><i class="fas fa-desktop"></i> Add Marks and SMS</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.enter-marks') }}"><i class="fas fa-code"></i> Update Marks and SMS</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.enter-marks') }}"><i class="fas fa-edit"></i> View Marks and SMS</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.reports') }}"><i class="fas fa-book-open"></i> Examination Reports</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-cogs me-2"></i>Exam Setup</h6></li>
                            <li><a class="dropdown-item" href="{{ route('exam.subjects') }}"><i class="fas fa-book"></i> Manage Subjects</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.grading-policy') }}"><i class="fas fa-percentage"></i> Grading Policy</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.manage') }}"><i class="fas fa-book-reader"></i> Manage Exams</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.standings') }}"><i class="fas fa-medal"></i> Manage Standings</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.academic-dates') }}"><i class="fas fa-calendar-day"></i> Academic Calendar</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.class-shifts') }}"><i class="fas fa-clock"></i> Manage Class Shifts</a></li>
                            <li><a class="dropdown-item" href="{{ route('exam.sms-templates') }}"><i class="fas fa-comment-dots"></i> SMS Templates</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-user-check me-2"></i>Student Attendance</h6></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.take') }}"><i class="fas fa-hand-paper"></i> Take / Update Attendance</a></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.absentees') }}"><i class="fas fa-eye-slash"></i> Absentees</a></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.sheet-selector') }}"><i class="fas fa-calendar-alt"></i> Attendance Sheet</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-file-medical-alt me-2"></i>Attendance Report</h6></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.combine-report') }}"><i class="fas fa-plus"></i> Combine Attendance Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.individual-report') }}"><i class="fas fa-file-alt"></i> Individual Attendance Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('attendance.summary') }}"><i class="fas fa-file"></i> Attendance Summary</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('sms.blaster') }}"><i class="fas fa-comment"></i> SMS Blaster</a></li>
                            <li><a class="dropdown-item" href="{{ route('sms.report') }}"><i class="fas fa-file"></i> SMS Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('sms.family-report') }}"><i class="fas fa-file"></i> Family SMS Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-bell"></i> App Notification Report</a></li>
                        </ul>
                    </li>

                    <!-- HRM -->
                    @if(!isset($currentTenant) || $currentTenant->hasModule('hrm'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="hrmDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1 text-warning"></i> HRM
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-user-tie me-2"></i>Manage Employees</h6></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.employees.directory') }}"><i class="fas fa-laptop-code"></i> Manage Employees</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.time-in-out') }}"><i class="fas fa-laptop-code"></i> Manage Time In/Out</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.assign-classes') }}"><i class="fas fa-sitemap"></i> Assign Classes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-cogs me-2"></i>HR Setup</h6></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.departments') }}"><i class="fas fa-building"></i> Manage Departments</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.employment-types') }}"><i class="fas fa-id-card"></i> Manage Employment Types</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.shifts') }}"><i class="fas fa-clock"></i> Manage Shifts</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.standings') }}"><i class="fas fa-medal"></i> Manage Standings</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.custom-fields') }}"><i class="fas fa-list-alt"></i> Employee Form Customization</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.payslip-template') }}"><i class="fas fa-file-invoice"></i> Manage Print Template</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-calendar-check me-2"></i>Attendance</h6></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.time-in-out') }}"><i class="fas fa-hand-pointer"></i> Take / Update Attendance</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.monthly-attendance-selector') }}"><i class="fas fa-calendar-alt"></i> Monthly Attendance</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.lock-attendance') }}"><i class="fas fa-lock"></i> Lock / Unlock Attendance</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-chart-bar me-2"></i>Attendance Report</h6></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.combine-attendance-report') }}"><i class="fas fa-plus"></i> Combine Attendance Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.individual-attendance-report') }}"><i class="fas fa-file"></i> Individual Attendance Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.attendance-report') }}"><i class="fas fa-file"></i> Employee Attendance Report</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-calendar-minus me-2"></i>Leave Management</h6></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.leave-request') }}"><i class="fas fa-plus"></i> Request Leave</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.leave-approvals') }}"><i class="fas fa-calendar-check"></i> Leave Approvals</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-money-check-alt me-2"></i>Salary &amp; Payroll</h6></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.allowance-types') }}"><i class="fas fa-money-check-alt"></i> Allowances / Deductions Catalog</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.salary-plan-manager') }}"><i class="fas fa-file-invoice-dollar"></i> Create / Update Salary Plan</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.salary-plan-approvals') }}"><i class="fas fa-file-signature"></i> Salary Plan Approvals</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.generate-salary') }}"><i class="fas fa-calculator"></i> Generate Monthly Salary</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.pay-salary') }}"><i class="fas fa-hand-holding-usd"></i> Pay / Print Salary</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.salary-report') }}"><i class="fas fa-file-invoice-dollar"></i> Employee Salary Report</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.salary-advance-request') }}"><i class="fas fa-hand-holding-usd"></i> Request Salary Advance</a></li>
                            <li><a class="dropdown-item" href="{{ route('hrm.salary-advance-approvals') }}"><i class="fas fa-check-double"></i> Salary Advance Approvals</a></li>
                        </ul>
                    </li>
                    @endif

                    <!-- GENERAL & HOUSE MANAGEMENT -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="generalDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-school me-1 text-warning"></i> General
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><a class="dropdown-item" href="{{ route('general.notices') }}"><i class="fas fa-bullhorn"></i> Notice Board</a></li>
                            <li><a class="dropdown-item" href="{{ route('general.houses') }}"><i class="fas fa-shield-alt"></i> Manage Houses</a></li>
                            <li><a class="dropdown-item" href="{{ route('general.student-diary') }}"><i class="fas fa-book"></i> Student Diary</a></li>
                            <li><a class="dropdown-item" href="{{ route('general.complaints') }}"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>
                            <li><a class="dropdown-item" href="{{ route('general.ptm-schedule') }}"><i class="fas fa-users-cog"></i> Parent-Teacher Meetings</a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Right Side: Configuration Modules -->
                <ul class="navbar-nav mb-2 mb-lg-0">
                    <!-- Core Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1 text-warning"></i> Core
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            @if(auth()->check() && auth()->user()->role == 'Developer')
                                <li><h6 class="dropdown-header text-uppercase text-danger">Tenant Switcher</h6></li>
                                @foreach(\App\Models\Tenant::all() as $t)
                                    <li>
                                        <button class="dropdown-item d-flex justify-content-between align-items-center" onclick="window.location.href='/switch-tenant/{{ $t->id }}'">
                                            <span><i class="fas fa-building text-danger"></i> {{ $t->name }}</span>
                                            @if(session('tenant_id') == $t->id) <i class="fas fa-check-circle text-success"></i> @endif
                                        </button>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><h6 class="dropdown-header text-uppercase text-muted">Organization</h6></li>
                            <li><a class="dropdown-item" href="/campuses"><i class="fas fa-building text-primary"></i> Campus</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-city text-primary"></i> City</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase text-muted">Academics</h6></li>
                            <li><a class="dropdown-item" href="/sessions"><i class="fas fa-calendar-alt text-primary"></i> Sessions</a></li>
                            <li><a class="dropdown-item" href="/classes"><i class="fas fa-layer-group text-primary"></i> Classes & Sections</a></li>
                            <li><a class="dropdown-item" href="/campus-classes"><i class="fas fa-network-wired text-primary"></i> Assign Classes to Campuses</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase text-muted">Financial Setup</h6></li>
                            <li><a class="dropdown-item" href="/fee-particulars"><i class="fas fa-list-ul text-primary"></i> Fee Particulars</a></li>
                            <li><a class="dropdown-item" href="/fee-plans"><i class="fas fa-file-invoice-dollar text-primary"></i> Fee Plan List</a></li>
                            <li><a class="dropdown-item" href="/fee-plan-mapping"><i class="fas fa-check-square text-primary"></i> Particulars Mapping</a></li>
                            <li><a class="dropdown-item" href="/fee-billing-setup"><i class="fas fa-calendar-check text-primary"></i> Monthly Billing Setup</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase text-muted">Policies</h6></li>
                            <li><a class="dropdown-item" href="/discount-types"><i class="fas fa-percent text-info"></i> Discount Types</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-ban text-info"></i> Late Fee Fine Policy</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-star text-info"></i> Student Attendance Fine</a></li>
                        </ul>
                    </li>

                    <!-- ADMIN Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-1 text-warning"></i> Admin
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                            <li><h6 class="dropdown-header text-uppercase text-muted">System Security</h6></li>
                            <li><a class="dropdown-item" href="/users-and-permissions"><i class="fas fa-users-cog text-primary"></i> Users & Permissions</a></li>
                            <li><a class="dropdown-item" href="{{ route('roles.manage') }}"><i class="fas fa-shield-alt text-primary"></i> Manage Roles & Permissions</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-history text-primary"></i> User Logging</a></li>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase text-muted">Services</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-broadcast-tower text-info"></i> SMS Gateway Settings</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-envelope-open-text text-info"></i> WhatsApp Service</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-certificate text-info"></i> Manage Certificates</a></li>

                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-uppercase text-muted">Attendance Tech</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-map-marker-alt text-success"></i> Geo Location Attendance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-qrcode text-success"></i> QR Biometric Attendance</a></li>
                        </ul>
                    </li>

                    @auth
                    <!-- User Profile Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fs-5 me-2"></i>
                            <span class="small fw-bold">{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-3" style="min-width: 200px;">
                            <li class="text-center mb-3">
                                <div class="bg-primary bg-opacity-10 py-3 rounded-4">
                                    <div class="fw-bold text-dark">{{ auth()->user()->username }}</div>
                                    <div class="tiny text-muted fw-bold text-uppercase">{{ auth()->user()->role ?? 'Administrator' }}</div>
                                </div>
                            </li>
                            <li><a class="dropdown-item rounded-3" href="#"><i class="fas fa-user-edit"></i> My Profile</a></li>
                            <li><a class="dropdown-item rounded-3" href="#"><i class="fas fa-key"></i> Change Password</a></li>
                            @if(auth()->user()->isSuperAdmin() && !auth()->user()->tenant_id)
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item rounded-3" href="{{ route('platform.tenants') }}"><i class="fas fa-building text-primary"></i> Platform Admin</a></li>
                                <li><a class="dropdown-item rounded-3" href="{{ route('select-tenant') }}"><i class="fas fa-exchange-alt text-primary"></i> Switch Institution</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold rounded-3">
                                        <i class="fas fa-sign-out-alt"></i> Logout System
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Dynamic Content -->
    <main class="content-container animate__animated animate__fadeIn">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Livewire Component Slot -->
        {{ $slot }}
    </main>

    <!-- Essential Scripts -->
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
