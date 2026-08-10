<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Challan;
use App\Models\Campus\Campus;
use App\Models\Finance\GLAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\ChallanVoidLog;
use Illuminate\Support\Facades\Auth;

class ManageChallans extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $campus_id = '';
    public $month = '';
    public $year = '';
    public $status = 'unpaid';
    // Filters by the STUDENT's active status (distinct from $status above,
    // which is the challan's own paid/unpaid status). Defaults to active so
    // day-to-day fee collection isn't cluttered with alumni challans; switch
    // to 'inactive' to specifically collect outstanding dues from a student
    // who has left/transferred (documented as its own workflow).
    public $student_status_filter = 'active';

    // Payment fields
    public $receiving_account_id = '';
    public $payment_date = '';
    public $challan_notes = '';
    
    // Selection
    public $selected_challans = [];
    public $selectAll = false;

    // Void modal (paid challans only -- captures a reason for the audit log)
    public $voidingChallanId = null;
    public $void_reason = '';
    public $isVoidModalOpen = false;

    public function mount()
    {
        $this->month = strtolower(date('M'));
        $this->year = date('Y');
        $this->payment_date = date('Y-m-d');
        
        // Auto-select cashier account if exists
        $cashierAccount = GLAccount::where('user_id', Auth::id())->first();
        if ($cashierAccount) {
            $this->receiving_account_id = $cashierAccount->id;
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected_challans = $this->getChallanQuery()->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected_challans = [];
        }
    }

    public function updated() { $this->resetPage(); }

    // markAsPaid() was removed: it was dead code (no button anywhere calls it)
    // that reimplemented payment/GL-posting outside FeeService with several
    // real bugs (double-counting a partially-paid challan's full total_amount,
    // a fragile hardcoded income-account lookup, and no paid_amount/receipt_no
    // tracking). Use PayFeeChallan (which posts through
    // FeeService::collectChallanPayment) to record a payment instead.

    public function deleteChallan($id)
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('fee.challan.payments')) {
            session()->flash('error', 'You are not authorized to delete challans.');
            return;
        }

        $challan = Challan::findOrFail($id);

        if ($challan->status === 'paid' || $challan->receipt_no) {
            session()->flash('error', 'This challan has a payment on it. Use the Void action to reverse it (a reason is logged for the audit trail).');
            return;
        }

        // If it was never paid, just delete the challan entirely
        $challan->items()->delete();
        $challan->delete();
        session()->flash('message', "Unpaid challan successfully deleted.");
    }

    public function openVoidModal($id)
    {
        $this->voidingChallanId = $id;
        $this->void_reason = '';
        $this->isVoidModalOpen = true;
    }

    public function closeVoidModal()
    {
        $this->isVoidModalOpen = false;
        $this->voidingChallanId = null;
        $this->void_reason = '';
    }

    public function confirmVoid()
    {
        $user = auth()->user();
        if (!$user->isSuperAdmin() && !$user->hasPermission('fee.challan.payments')) {
            session()->flash('error', 'You are not authorized to void challans.');
            return;
        }

        $this->validate(['void_reason' => 'required|string|max:255']);

        $challan = Challan::with('student')->findOrFail($this->voidingChallanId);

        if ($challan->status !== 'paid' && !$challan->receipt_no) {
            $this->closeVoidModal();
            session()->flash('error', 'This challan has no payment to void.');
            return;
        }

        $voidedAmount = (float) $challan->paid_amount + (float) $challan->discount_amount;

        if ($challan->receipt_no) {
            $journalEntries = JournalEntry::where('voucher_no', $challan->receipt_no)->get();
        } else {
            // Fallback for older entries where receipt_no was not stored
            $journalEntries = JournalEntry::where('narration', 'like', "%{$challan->challan_no}%")->get();
        }

        foreach ($journalEntries as $entry) {
            $entry->items()->delete();
            $entry->delete();
        }

        // Revert challan back to unpaid state instead of deleting it
        $challan->update([
            'status' => 'unpaid',
            'paid_amount' => 0,
            'discount_amount' => 0,
            'receiving_account_id' => null,
            'discount_account_id' => null,
            'paid_date' => null,
            'receipt_no' => null,
            'challan_notes' => null,
            'paid_by' => null,
        ]);

        ChallanVoidLog::create([
            'tenant_id' => session('tenant_id') ?? Auth::user()->tenant_id,
            'challan_id' => $challan->id,
            'challan_no' => $challan->challan_no,
            'student_id' => $challan->student_id,
            'student_name' => trim(($challan->student->first_name ?? '') . ' ' . ($challan->student->last_name ?? '')),
            'voided_amount' => $voidedAmount,
            'void_reason' => $this->void_reason,
            'voided_by' => Auth::id(),
        ]);

        $this->closeVoidModal();
        session()->flash('message', "Payment successfully voided. Ledger entries reversed and challan is unpaid again.");
    }

    public function render()
    {
        $query = $this->getChallanQuery();

        return view('livewire.manage-challans', [
            'challans' => $query->latest()->paginate(15),
            'campuses' => Campus::all(),
            'months' => ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
            'receiving_accounts' => GLAccount::whereIn('type', ['asset', 'bank', 'cash'])->get()
        ])->layout('layouts.app');
    }

    protected function getChallanQuery()
    {
        $query = Challan::with(['student.campus', 'student.schoolClass', 'student.section', 'items'])
            ->where('year', $this->year);

        if ($this->month) {
            $query->where('month', $this->month);
        }

        if ($this->status === 'unpaid' || $this->status === 'pending') {
            $query->whereIn('status', ['unpaid', 'pending']);
        } elseif ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->campus_id) {
            $query->whereHas('student', function($q) {
                $q->where('campus_id', $this->campus_id);
            });
        }

        if ($this->student_status_filter !== 'all') {
            $isActive = $this->student_status_filter === 'active';
            $query->whereHas('student', fn ($q) => $q->where('is_active', $isActive));
        }

        if ($this->search) {
            $query->whereHas('student', function($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                  ->orWhere('last_name', 'like', '%' . $this->search . '%')
                  ->orWhere('admission_no', 'like', '%' . $this->search . '%')
                  ->orWhere('challan_no', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query;
    }
}
