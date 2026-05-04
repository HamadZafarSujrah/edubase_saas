<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignUserPermissions extends Component
{
    public $userId;
    public $user;
    public $permissions = [];
    public $categories = [];

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->user = User::findOrFail($userId);
        $this->loadPermissions();
        $this->defineModules();
    }

    public function loadPermissions()
    {
        $this->permissions = DB::table('user_module_permissions')
            ->where('user_id', $this->userId)
            ->pluck('is_allowed', 'module_key')
            ->toArray();
    }

    public function defineModules()
    {
        $this->categories = [
            'System Administration' => [
                'admin_campuses' => 'Manage Campuses',
                'admin_sessions' => 'Manage Sessions',
                'admin_classes' => 'Classes & Sections',
                'admin_fee_setup' => 'Fee Setup & Particulars',
                'admin_users' => 'Users & Permissions',
            ],
            'Admissions' => [
                'admission_inquiry' => 'Student Inquiry',
                'admission_create' => 'New Admission',
                'admission_view' => 'Student List',
                'admission_edit' => 'Edit Student Profile',
                'admission_delete' => 'Delete Student',
            ],
            'Finance & GL' => [
                'finance_gl_groups' => 'GL Account Groups',
                'finance_gl_accounts' => 'GL Accounts/Ledgers',
                'finance_journal' => 'Journal Entry',
                'finance_inquiry' => 'General Ledger Inquiry',
                'finance_trial_balance' => 'Trial Balance',
                'finance_pl' => 'P & L Statement',
            ],
            'Fee Management' => [
                'fee_collection' => 'Cash Collection',
                'fee_billing' => 'Generate Monthly Challans',
                'fee_reports' => 'Detailed Fee Reports',
                'fee_reversal' => 'Void/Reverse Transactions',
            ],
            'Exam & Academics' => [
                'exam_marks_add' => 'Add/Update Marks',
                'exam_reports' => 'Examination Reports',
                'exam_cards' => 'Individual Result Cards',
            ],
            'HRM & Payroll' => [
                'hrm_employees' => 'Manage Employees',
                'hrm_attendance' => 'Staff Attendance',
                'hrm_payroll' => 'Manage Salaries',
            ]
        ];
    }

    public function save()
    {
        DB::transaction(function() {
            DB::table('user_module_permissions')->where('user_id', $this->userId)->delete();
            
            foreach($this->permissions as $key => $allowed) {
                if ($allowed) {
                    DB::table('user_module_permissions')->insert([
                        'user_id' => $this->userId,
                        'module_key' => $key,
                        'is_allowed' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });

        session()->flash('message', 'Module permissions updated successfully for ' . $this->user->name);
    }

    public function toggleAll($categoryName = null)
    {
        if ($categoryName) {
            $categoryModules = $this->categories[$categoryName];
            $anyMissing = false;
            foreach ($categoryModules as $key => $label) {
                if (empty($this->permissions[$key])) { $anyMissing = true; break; }
            }
            foreach ($categoryModules as $key => $label) {
                $this->permissions[$key] = $anyMissing;
            }
        } else {
            $anyMissing = false;
            foreach ($this->categories as $modules) {
                foreach ($modules as $key => $label) {
                    if (empty($this->permissions[$key])) { $anyMissing = true; break; }
                }
            }
            foreach ($this->categories as $modules) {
                foreach ($modules as $key => $label) {
                    $this->permissions[$key] = $anyMissing;
                }
            }
        }
    }

    public function isCategoryFull($categoryName)
    {
        $modules = $this->categories[$categoryName];
        foreach ($modules as $key => $label) {
            if (empty($this->permissions[$key])) return false;
        }
        return true;
    }

    public function render()
    {
        return view('livewire.assign-user-permissions')->layout('layouts.app');
    }
}
