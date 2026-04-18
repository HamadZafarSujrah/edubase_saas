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
            
            <div class="collapse navbar-collapse" id="mainNav">
                
                <!-- Left Side: Management Modules -->
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-home me-1"></i> Home</a>
                    </li>

                    <!-- ADMISSIONS -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="admissionsDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-graduate me-1 text-warning"></i> Admissions
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><a class="dropdown-item" href="/student-admission"><i class="fas fa-handshake"></i> New Admission</a></li>
                            <li><a class="dropdown-item" href="/manage-students"><i class="fas fa-users-cog"></i> Manage Students</a></li>
                            <li><a class="dropdown-item" href="/fee-plans"><i class="fas fa-file-invoice-dollar text-primary"></i> Create Fee Plans</a></li>
                            <li><a class="dropdown-item" href="/manage-students"><i class="fas fa-edit text-warning"></i> View/Edit Student Fee Plans</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/generate-challans"><i class="fas fa-cog"></i> Generate Challan</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Generate Challan Installment Wise</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-plus-circle"></i> Add Amount in Generated Challan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/pay-print-challans"><i class="fas fa-print"></i> Pay or Print Fee Challans</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-money-bill-wave"></i> Direct Payment</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-users"></i> Familywise Payment</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-balance-scale"></i> Paid Challans</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-minus"></i> Challan Discounts</a></li>
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
                            <li><a class="dropdown-item" href="#"><i class="fas fa-print"></i> Print Fee Reminder</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-friends"></i> All Students</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-exchange-alt"></i> Batch Transfer/Promote Students</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-paper-plane"></i> Print Certificates or Date Sheets</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-clipboard-list"></i> Certificates Logs</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-plus"></i> Add Bulk Students</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-check"></i> Students By User</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-qrcode"></i> Student QR Code</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-alt"></i> Date Sheet</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-id-card"></i> Roll No. Slip</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chart-line"></i> Student Yearly Fee Increment</a></li>
                        </ul>
                    </li>

                    <!-- FINANCE -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="financeDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-file-invoice me-1 text-warning"></i> Finance
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-check"></i> Accounting Periods</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-project-diagram"></i> GL Groups</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-list-ol"></i> GL Accounts</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-stream"></i> Chart of Accounts</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-dollar-sign"></i> Journal Entry</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cut"></i> Add Expense</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-hand-holding-usd"></i> Add Other Income</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-search-dollar"></i> Journal Inquiry</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-th"></i> GL Inquiry</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-columns"></i> General Ledger</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-exchange-alt"></i> Trial Balance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-balance-scale"></i> P & L Statement</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chess-knight"></i> Balance Sheet</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-chess-rook"></i> Cash Report</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#"><i class="fas fa-times-circle"></i> Voided Transactions</a></li>
                        </ul>
                    </li>

                    <!-- EXAM & ATTENDANCE -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="examDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1 text-warning"></i> Exam & Attendance
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-book-reader me-2"></i>Examination</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-desktop"></i> Add Marks and SMS</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-code"></i> Update Marks and SMS</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-edit"></i> View Marks and SMS</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-book-open"></i> Examination Reports</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-user-check me-2"></i>Student Attendance</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-hand-paper"></i> Take Attendance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye"></i> View/Update Attendance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye-slash"></i> Absentees</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-alt"></i> Attendance Sheet</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-file-medical-alt me-2"></i>Attendance Report</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-plus"></i> Combine Attendance Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-alt"></i> Individual Attendance Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file"></i> Attendance Summary</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-comment"></i> SMS Blaster</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file"></i> SMS Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file"></i> Family SMS Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-bell"></i> App Notification Report</a></li>
                        </ul>
                    </li>

                    <!-- HRM -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="hrmDrop" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1 text-warning"></i> HRM
                        </a>
                        <ul class="dropdown-menu shadow-lg">
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-user-tie me-2"></i>Manage Employees</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-laptop-code"></i> Manage Employees</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-laptop-code"></i> Manage Time In/Out</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-sitemap"></i> Assign Classes</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-calendar-check me-2"></i>Attendance</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-hand-pointer"></i> Take Attendance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-eye"></i> View/Update Attendance</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-calendar-alt"></i> Monthly Attendance</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header text-dark fw-bold"><i class="fas fa-chart-bar me-2"></i>Attendance Report</h6></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-plus"></i> Combine Attendance Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file"></i> Individual Attendance Report</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file"></i> Employee Attendance Report</a></li>
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
                            <li><a class="dropdown-item" href="#"><i class="fas fa-percent text-info"></i> Discount Types</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-ban text-info"></i> Late Fee Fine Policy</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-star text-info"></i> Student Attendance Fine</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-circle fs-5"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Dynamic Content -->
    <main class="content-container animate__animated animate__fadeIn">
        {{ $slot }}
    </main>

    <!-- Required Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>
