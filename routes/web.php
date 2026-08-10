<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\UsersAndPermissions;
use App\Livewire\GLAccounts;
use App\Livewire\GLAccountGroups;
use App\Livewire\ManageRoles;
use App\Livewire\StudentDirectory;
use App\Livewire\StudentAdmission;
use App\Livewire\CampusManager;
use App\Livewire\ClassManager;
use App\Livewire\SessionManager;
use App\Livewire\GenerateChallan;
use App\Livewire\GenerateInstallmentChallan;
use App\Livewire\ManageChallans;
use App\Livewire\PaidChallans;
use App\Livewire\PayFeeChallan;
use App\Livewire\AddChallanAmount;
use App\Livewire\DirectPayment;
use App\Livewire\FamilyWisePayment;
use App\Livewire\VoidedTransactionsReport;
use App\Livewire\StudentsByUser;
use App\Livewire\JournalEntryForm;
use App\Livewire\GeneralLedger;
use App\Livewire\SelectTenant;
use App\Livewire\ChartOfAccounts;
use App\Livewire\AccountingPeriods;
use App\Livewire\FeePlanManager;
use App\Livewire\FeeParticularManager;
use App\Livewire\FeeBillingSetup;
use App\Livewire\FeePlanMapping;
use App\Livewire\StudentProfile;
use App\Livewire\CampusClassManager;
use App\Livewire\StudentFeePlanList;
use App\Livewire\DiscountTypeManager;
use App\Livewire\TrialBalance;
use App\Livewire\ProfitAndLoss;
use App\Livewire\BalanceSheet;
use App\Livewire\CashReport;
use App\Livewire\JournalInquiry;
use App\Livewire\GLInquiry;
use App\Livewire\AddExpense;
use App\Livewire\AddOtherIncome;
use App\Livewire\AddBulkStudents;
use App\Livewire\BatchPromoteStudents;
use App\Livewire\StudentFeeIncrement;
use App\Livewire\PrintFeeReminder;
use App\Livewire\IssueCertificate;
use App\Livewire\CertificatesLog;
use App\Livewire\StudentQRCode;
use App\Livewire\TakeAttendance;
use App\Livewire\Absentees;
use App\Livewire\AttendanceSheetSelector;
use App\Livewire\CombineAttendanceReport;
use App\Livewire\IndividualAttendanceReport;
use App\Livewire\AttendanceSummary;
use App\Livewire\SmsBlaster;
use App\Livewire\SmsReport;
use App\Livewire\FamilySmsReport;
use App\Livewire\DepartmentManager;
use App\Livewire\EmploymentTypeManager;
use App\Livewire\ShiftManager;
use App\Livewire\EmployeeDirectory;
use App\Livewire\EmployeeForm;
use App\Livewire\AssignClasses;
use App\Livewire\EmployeeTimeInOut;
use App\Livewire\EmployeeMonthlyAttendanceSelector;
use App\Livewire\CombineEmployeeAttendanceReport;
use App\Livewire\IndividualEmployeeAttendanceReport;
use App\Livewire\EmployeeAttendanceReport;
use App\Livewire\RequestLeave;
use App\Livewire\LeaveApprovals;
use App\Livewire\SalaryPlanManager;
use App\Livewire\SalaryPlanApprovals;
use App\Livewire\GenerateSalary;
use App\Livewire\PaySalary;
use App\Livewire\EmployeeSalaryReport;
use App\Livewire\RequestSalaryAdvance;
use App\Livewire\SalaryAdvanceApprovals;
use App\Livewire\AllowanceTypeManager;
use App\Livewire\EmployeeStandingManager;
use App\Livewire\EmployeeCustomFieldManager;
use App\Livewire\LockAttendance;
use App\Livewire\PayslipTemplateManager;
use App\Livewire\NoticeManager;
use App\Livewire\HouseManager;
use App\Livewire\DiaryManager;
use App\Livewire\ComplaintManager;
use App\Livewire\PtmScheduler;
use App\Livewire\SubjectManager;
use App\Livewire\GradingPolicyManager;
use App\Livewire\ExamManager;
use App\Livewire\StandingManager;
use App\Livewire\AcademicDateManager;
use App\Livewire\ClassShiftManager;
use App\Livewire\SmsTemplateManager;
use App\Livewire\DateSheetManager;
use App\Livewire\RollNoSlipSelector;
use App\Http\Controllers\DateSheetController;
use App\Http\Controllers\RollNoSlipController;
use App\Livewire\EnterMarks;
use App\Livewire\ExaminationReports;
use App\Http\Controllers\PrintChallanController;
use App\Http\Controllers\PrintFeeReminderController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\AttendanceSheetController;
use App\Http\Controllers\EmployeeAttendanceSheetController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ResultCardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register-school', \App\Livewire\Auth\RegisterSchool::class)->name('register-school')->middleware('guest');

Route::middleware(['auth'])->group(function () {
    
    // Central Routes (No Tenant Required)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/select-tenant', SelectTenant::class)->name('select-tenant');
    Route::get('/platform/tenants', \App\Livewire\TenantManager::class)->name('platform.tenants');
    Route::get('/platform/invoices', \App\Livewire\TenantInvoiceManager::class)->name('platform.tenant-invoices');
    
    Route::match(['get', 'post'], '/logout', function () {
        auth()->logout();
        session()->forget('tenant_id');
        return redirect()->route('login');
    })->name('logout');

    // School-Specific Routes (Tenant Required)
    Route::middleware(['tenant'])->group(function () {
        
        // System Administration
        Route::get('/users-and-permissions', UsersAndPermissions::class)->name('users.permissions');
        Route::get('/manage-roles', ManageRoles::class)->name('roles.manage');

        // Finance Management
        Route::get('/chart-of-accounts', \App\Livewire\ChartOfAccounts::class)->name('finance.chart_of_accounts');
        Route::get('/accounting-periods', \App\Livewire\AccountingPeriods::class)->name('finance.accounting_periods');
        Route::get('/gl-accounts', GLAccounts::class)->name('gl.accounts');
        Route::get('/gl-groups', GLAccountGroups::class)->name('gl.groups');
        Route::get('/generate-challan', GenerateChallan::class)->name('finance.generate-challan');
        Route::get('/manage-challans', ManageChallans::class)->name('finance.manage-challans');
        Route::get('/journal-entry', JournalEntryForm::class)->name('finance.journal-entry');
        Route::get('/add-expense', AddExpense::class)->name('finance.add-expense');
        Route::get('/add-other-income', AddOtherIncome::class)->name('finance.add-other-income');
        Route::get('/journal-inquiry', JournalInquiry::class)->name('finance.journal-inquiry');
        Route::get('/gl-inquiry', GLInquiry::class)->name('finance.gl-inquiry');
        Route::get('/general-ledger', GeneralLedger::class)->name('finance.general-ledger');
        Route::get('/trial-balance', TrialBalance::class)->name('finance.trial-balance');
        Route::get('/profit-loss', ProfitAndLoss::class)->name('finance.profit-loss');
        Route::get('/balance-sheet', BalanceSheet::class)->name('finance.balance-sheet');
        Route::get('/cash-report', CashReport::class)->name('finance.cash-report');

        // Academic Management
        Route::get('/manage-students', StudentDirectory::class)->name('students.directory');
        Route::get('/student-admission', StudentAdmission::class)->name('students.admission');
        Route::get('/student-profile/{id}', StudentProfile::class)->name('students.profile');
        Route::get('/add-bulk-students', AddBulkStudents::class)->name('students.add-bulk');
        Route::get('/batch-promote-students', BatchPromoteStudents::class)->name('students.batch-promote');
        Route::get('/student-fee-increment', StudentFeeIncrement::class)->name('students.fee-increment');
        Route::get('/print-fee-reminder', PrintFeeReminder::class)->name('students.print-fee-reminder');
        Route::get('/print-fee-reminders', [PrintFeeReminderController::class, 'bulkPrint'])->name('print-fee-reminders');
        Route::get('/issue-certificate', IssueCertificate::class)->name('certificates.issue');
        Route::get('/certificates-log', CertificatesLog::class)->name('certificates.log');
        Route::get('/print-certificate/{id}', [CertificateController::class, 'print'])->name('print-certificate');
        Route::get('/download-certificate/{id}', [CertificateController::class, 'downloadPdf'])->name('download-certificate');
        Route::get('/student-qr-code', StudentQRCode::class)->name('students.qr-code');
        Route::get('/students-by-user', StudentsByUser::class)->name('students.by-user');

        // Aliases for Sidebar Links
        Route::get('/campus-manager', CampusManager::class)->name('campus.manager');
        Route::get('/campuses', CampusManager::class); 
        
        Route::get('/class-manager', ClassManager::class)->name('class.manager');
        Route::get('/classes', ClassManager::class); 
        
        Route::get('/session-manager', SessionManager::class)->name('session.manager');
        Route::get('/sessions', SessionManager::class); 
        
        Route::get('/campus-classes', CampusClassManager::class)->name('campus.classes');

        // Advanced Finance/Fees Management
        Route::get('/fee-plans', FeePlanManager::class)->name('finance.fee-plans');
        Route::get('/fee-particulars', FeeParticularManager::class)->name('finance.fee-particulars');
        Route::get('/fee-billing-setup', FeeBillingSetup::class)->name('finance.fee-billing-setup');
        Route::get('/fee-plan-mapping', FeePlanMapping::class)->name('finance.fee-plan-mapping');
        Route::get('/discount-types', DiscountTypeManager::class)->name('finance.discount-types');
        Route::get('/create-fee-plans', StudentFeePlanList::class)->name('create.fee-plans');
        Route::get('/create-fee-plans/form', \App\Livewire\StudentFeePlanEditor::class)->name('create.fee-plan.form');
        Route::get('/view-edit-fee-plans', StudentFeePlanList::class)->name('finance.view-edit-fee-plans');
        
        Route::get('/generate-challans', GenerateChallan::class)->name('finance.generate-challans');
        Route::get('/generate-installment-challans', GenerateInstallmentChallan::class)->name('finance.generate-installment-challans');
        Route::get('/pay-print-challans', ManageChallans::class)->name('finance.pay-print-challans');
        Route::get('/paid-challans', PaidChallans::class)->name('finance.paid-challans');
        Route::get('/pay-fee-challan', PayFeeChallan::class)->name('finance.pay-fee-challan');
        Route::get('/add-challan-amount', AddChallanAmount::class)->name('finance.add-challan-amount');
        Route::get('/direct-payment', DirectPayment::class)->name('finance.direct-payment');
        Route::get('/family-payment', FamilyWisePayment::class)->name('finance.family-payment');
        Route::get('/voided-transactions', VoidedTransactionsReport::class)->name('finance.voided-transactions');
        Route::get('/print-challans', [PrintChallanController::class, 'bulkPrint'])->name('print-challans');
        Route::get('/download-challans', [PrintChallanController::class, 'downloadPdf'])->name('download-challans');

        // Attendance
        Route::get('/take-attendance', TakeAttendance::class)->name('attendance.take');
        Route::get('/absentees', Absentees::class)->name('attendance.absentees');
        Route::get('/attendance-sheet', AttendanceSheetSelector::class)->name('attendance.sheet-selector');
        Route::get('/print-attendance-sheet', [AttendanceSheetController::class, 'print'])->name('print-attendance-sheet');
        Route::get('/combine-attendance-report', CombineAttendanceReport::class)->name('attendance.combine-report');
        Route::get('/individual-attendance-report', IndividualAttendanceReport::class)->name('attendance.individual-report');
        Route::get('/attendance-summary', AttendanceSummary::class)->name('attendance.summary');

        // SMS
        Route::get('/sms-blaster', SmsBlaster::class)->name('sms.blaster');
        Route::get('/sms-report', SmsReport::class)->name('sms.report');
        Route::get('/family-sms-report', FamilySmsReport::class)->name('sms.family-report');

        // HRM: Employee Directory foundation
        Route::get('/hrm/departments', DepartmentManager::class)->name('hrm.departments');
        Route::get('/hrm/employment-types', EmploymentTypeManager::class)->name('hrm.employment-types');
        Route::get('/hrm/shifts', ShiftManager::class)->name('hrm.shifts');
        Route::get('/hrm/employees', EmployeeDirectory::class)->name('hrm.employees.directory');
        Route::get('/hrm/employees/create', EmployeeForm::class)->name('hrm.employees.create');
        Route::get('/hrm/employees/{id}/edit', EmployeeForm::class)->name('hrm.employees.edit');
        Route::get('/hrm/assign-classes', AssignClasses::class)->name('hrm.assign-classes');

        // HRM: Employee Attendance
        Route::get('/hrm/time-in-out', EmployeeTimeInOut::class)->name('hrm.time-in-out');
        Route::get('/hrm/monthly-attendance', EmployeeMonthlyAttendanceSelector::class)->name('hrm.monthly-attendance-selector');
        Route::get('/print-employee-attendance-sheet', [EmployeeAttendanceSheetController::class, 'print'])->name('print-employee-attendance-sheet');
        Route::get('/hrm/combine-attendance-report', CombineEmployeeAttendanceReport::class)->name('hrm.combine-attendance-report');
        Route::get('/hrm/individual-attendance-report', IndividualEmployeeAttendanceReport::class)->name('hrm.individual-attendance-report');
        Route::get('/hrm/attendance-report', EmployeeAttendanceReport::class)->name('hrm.attendance-report');

        // HRM: Leave Management
        Route::get('/hrm/leave-request', RequestLeave::class)->name('hrm.leave-request');
        Route::get('/hrm/leave-approvals', LeaveApprovals::class)->name('hrm.leave-approvals');

        // HRM: Salary & Payroll
        Route::get('/hrm/salary-plan', SalaryPlanManager::class)->name('hrm.salary-plan-manager');
        Route::get('/hrm/salary-plan-approvals', SalaryPlanApprovals::class)->name('hrm.salary-plan-approvals');
        Route::get('/hrm/generate-salary', GenerateSalary::class)->name('hrm.generate-salary');
        Route::get('/hrm/pay-salary', PaySalary::class)->name('hrm.pay-salary');
        Route::get('/hrm/salary-report', EmployeeSalaryReport::class)->name('hrm.salary-report');
        Route::get('/hrm/salary-advance-request', RequestSalaryAdvance::class)->name('hrm.salary-advance-request');
        Route::get('/hrm/salary-advance-approvals', SalaryAdvanceApprovals::class)->name('hrm.salary-advance-approvals');
        Route::get('/print-payslip/{id}', [PayslipController::class, 'print'])->name('print-payslip');

        // HRM: Settings extras
        Route::get('/hrm/allowance-types', AllowanceTypeManager::class)->name('hrm.allowance-types');
        Route::get('/hrm/standings', EmployeeStandingManager::class)->name('hrm.standings');
        Route::get('/hrm/custom-fields', EmployeeCustomFieldManager::class)->name('hrm.custom-fields');
        Route::get('/hrm/lock-attendance', LockAttendance::class)->name('hrm.lock-attendance');
        Route::get('/hrm/payslip-template', PayslipTemplateManager::class)->name('hrm.payslip-template');

        // Module 7: General & House Management
        Route::get('/notices', NoticeManager::class)->name('general.notices');
        Route::get('/houses', HouseManager::class)->name('general.houses');
        Route::get('/student-diary', DiaryManager::class)->name('general.student-diary');
        Route::get('/complaints', ComplaintManager::class)->name('general.complaints');
        Route::get('/ptm-schedule', PtmScheduler::class)->name('general.ptm-schedule');

        // Module 5: Examination
        Route::get('/subjects', SubjectManager::class)->name('exam.subjects');
        Route::get('/grading-policy', GradingPolicyManager::class)->name('exam.grading-policy');
        Route::get('/exams', ExamManager::class)->name('exam.manage');
        Route::get('/standings', StandingManager::class)->name('exam.standings');
        Route::get('/academic-dates', AcademicDateManager::class)->name('exam.academic-dates');
        Route::get('/class-shifts', ClassShiftManager::class)->name('exam.class-shifts');
        Route::get('/sms-templates', SmsTemplateManager::class)->name('exam.sms-templates');
        Route::get('/date-sheet', DateSheetManager::class)->name('exam.date-sheet');
        Route::get('/print-date-sheet/{examId}', [DateSheetController::class, 'print'])->name('print-date-sheet');
        Route::get('/roll-no-slips', RollNoSlipSelector::class)->name('exam.roll-no-slips');
        Route::get('/print-roll-no-slips', [RollNoSlipController::class, 'print'])->name('print-roll-no-slips');
        Route::get('/enter-marks', EnterMarks::class)->name('exam.enter-marks');
        Route::get('/examination-reports', ExaminationReports::class)->name('exam.reports');
        Route::get('/print-result-card/{examId}/{studentId}', [ResultCardController::class, 'print'])->name('print-result-card');

        // SaaS Mode: tenant-side settings
        Route::get('/my-subscription', \App\Livewire\MySubscription::class)->name('finance.my-subscription');
        Route::get('/sms-gateway-settings', \App\Livewire\SmsGatewaySettings::class)->name('admin.sms-gateway-settings');
        Route::get('/whatsapp-settings', \App\Livewire\WhatsAppSettings::class)->name('admin.whatsapp-settings');
    });

    // Developer Tools
    Route::get('/switch-tenant/{id}', function ($id) {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            abort(403);
        }

        $tenant = \App\Models\Tenant::find($id);
        if (!$tenant || !$tenant->isActive()) {
            return back()->with('error', 'That institution does not exist or is not active.');
        }

        session(['tenant_id' => $tenant->id]);
        return back()->with('message', 'Switched to Institution: ' . $tenant->name);
    })->name('switch.tenant');
});
