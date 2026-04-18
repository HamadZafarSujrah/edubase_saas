<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/campuses'); // Or dashboard later
});

Route::get('/campuses', \App\Http\Livewire\CampusManager::class)->name('campuses');
Route::get('/sessions', \App\Http\Livewire\SessionManager::class)->name('sessions');
Route::get('/classes', \App\Http\Livewire\ClassManager::class)->name('classes');
Route::get('/campus-classes', \App\Http\Livewire\CampusClassManager::class)->name('campus-classes');
Route::get('/student-admission', \App\Http\Livewire\StudentAdmission::class)->name('student-admission');
Route::get('/manage-students', \App\Http\Livewire\StudentDirectory::class)->name('manage-students');

// Fee Setup Routes
Route::get('/fee-particulars', \App\Http\Livewire\FeeParticularManager::class)->name('fee-particulars');
Route::get('/fee-plans', \App\Http\Livewire\FeePlanManager::class)->name('fee-plans');
Route::get('/fee-plan-mapping', \App\Http\Livewire\FeePlanMapping::class)->name('fee-plan-mapping');
Route::get('/fee-billing-setup', \App\Http\Livewire\FeeBillingSetup::class)->name('fee-billing-setup');
Route::get('/generate-challans', \App\Http\Livewire\GenerateChallan::class)->name('generate-challans');
Route::get('/pay-print-challans', \App\Http\Livewire\ManageChallans::class)->name('pay-print-challans');

// Student-Specific Fee Editing (Per Screenshot 2)
Route::get('/view-edit-fee-plans', \App\Http\Livewire\StudentFeePlanList::class)->name('view-edit-fee-plans');
Route::get('/student-fee-plan/{id}', \App\Http\Livewire\StudentFeePlanEditor::class)->name('student-fee-plan');
Route::get('/student-profile/{id}', \App\Http\Livewire\StudentProfile::class)->name('student-profile');

// Printing Routes
Route::get('/print-challans', [App\Http\Controllers\PrintChallanController::class, 'bulkPrint'])->name('print-challans');
