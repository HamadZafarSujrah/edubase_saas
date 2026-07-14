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
use App\Livewire\ManageChallans;
use App\Livewire\PaidChallans;
use App\Livewire\PayFeeChallan;
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
use App\Http\Controllers\PrintChallanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware(['auth'])->group(function () {
    
    // Central Routes (No Tenant Required)
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/select-tenant', SelectTenant::class)->name('select-tenant');
    
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
        Route::get('/general-ledger', GeneralLedger::class)->name('finance.general-ledger');

        // Academic Management
        Route::get('/manage-students', StudentDirectory::class)->name('students.directory');
        Route::get('/student-admission', StudentAdmission::class)->name('students.admission');
        Route::get('/student-profile/{id}', StudentProfile::class)->name('students.profile');
        
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
        Route::get('/create-fee-plans', StudentFeePlanList::class)->name('create.fee-plans');
        Route::get('/create-fee-plans/form', \App\Livewire\StudentFeePlanEditor::class)->name('create.fee-plan.form');
        Route::get('/view-edit-fee-plans', StudentFeePlanList::class)->name('finance.view-edit-fee-plans');
        
        Route::get('/generate-challans', GenerateChallan::class)->name('finance.generate-challans');
        Route::get('/pay-print-challans', ManageChallans::class)->name('finance.pay-print-challans');
        Route::get('/paid-challans', PaidChallans::class)->name('finance.paid-challans');
        Route::get('/pay-fee-challan', PayFeeChallan::class)->name('finance.pay-fee-challan');
        Route::get('/print-challans', [PrintChallanController::class, 'bulkPrint'])->name('print-challans');
        Route::get('/download-challans', [PrintChallanController::class, 'downloadPdf'])->name('download-challans');
    });

    // Developer Tools
    Route::get('/switch-tenant/{id}', function ($id) {
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            session(['tenant_id' => $id]);
            return back()->with('message', 'Switched to Institution ID: ' . $id);
        }
        abort(403);
    })->name('switch.tenant');
});
