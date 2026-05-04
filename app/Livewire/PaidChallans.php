<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Challan;
use App\Models\Campus\Campus;
use App\Models\Finance\GLAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PaidChallans extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $campus_id = '';
    public $month = '';
    public $year = '';
    public $status = 'paid';

    // Payment fields
    public $receiving_account_id = '';
    public $payment_date = '';
    public $challan_notes = '';
    
    // Selection
    public $selected_challans = [];
    public $selectAll = false;

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

    public function markAsPaid($id = null)
    {
        $ids = $id ? [$id] : $this->selected_challans;

        if (empty($ids)) {
            session()->flash('error', "Please select at least one challan to pay.");
            return;
        }

        if (!$this->receiving_account_id) {
            session()->flash('error', "Please select a receiving account.");
            return;
        }

        $cashierAccount = GLAccount::find($this->receiving_account_id);
        $incomeAccount = GLAccount::where('type', 'income')->where('code', 'LIKE', '7%')->first();

        DB::transaction(function() use ($ids, $cashierAccount, $incomeAccount) {
            $challans = Challan::whereIn('id', $ids)->where('status', '!=', 'paid')->get();

            foreach ($challans as $challan) {
                $challan->update(['status' => 'paid']);

                // Create Automatic Journal Entry
                $entry = JournalEntry::create([
                    'tenant_id' => session('tenant_id'),
                    'campus_id' => $challan->student->campus_id,
                    'transaction_date' => $this->payment_date,
                    'voucher_no' => 'RCPT-' . time() . '-' . $challan->id,
                    'narration' => "Fee Received: " . ($this->challan_notes ?: "Monthly Fee for {$challan->month}/{$challan->year}") . " - {$challan->student->first_name}",
                    'created_by' => Auth::id(),
                ]);

                // Debit Cash/Bank
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id' => $cashierAccount->id,
                    'debit' => $challan->total_amount,
                    'credit' => 0,
                    'item_memo' => "Fee received from {$challan->student->admission_no}"
                ]);

                // Credit Revenue
                JournalItem::create([
                    'journal_entry_id' => $entry->id,
                    'gl_account_id' => $incomeAccount->id,
                    'debit' => 0,
                    'credit' => $challan->total_amount,
                    'item_memo' => "Revenue recognized"
                ]);
            }
        });

        $this->selected_challans = [];
        $this->selectAll = false;
        session()->flash('message', "SUCCESS: " . count($ids) . " challan(s) marked as paid.");
    }

    public function deleteChallan($id)
    {
        $challan = Challan::findOrFail($id);
        
        // If it was paid, we need to reverse the journal entries
        if ($challan->status === 'paid' || $challan->receipt_no) {
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

            session()->flash('message', "Payment successfully voided. Ledger entries reversed and challan is unpaid again.");
            return;
        }

        // If it was never paid, just delete the challan entirely
        $challan->items()->delete();
        $challan->delete();
        session()->flash('message', "Unpaid challan successfully deleted.");
    }

    public function render()
    {
        $query = $this->getChallanQuery();

        return view('livewire.paid-challans', [
            'challans' => $query->latest()->paginate(15),
            'campuses' => Campus::all(),
            'months' => ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'],
            'receiving_accounts' => GLAccount::whereIn('type', ['asset', 'bank', 'cash'])->get()
        ])->layout('layouts.app');
    }

    protected function getChallanQuery()
    {
        $query = Challan::with(['student.campus', 'student.schoolClass', 'items'])
            ->where('year', $this->year);

        if ($this->month) {
            $query->where('month', $this->month);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->campus_id) {
            $query->whereHas('student', function($q) {
                $q->where('campus_id', $this->campus_id);
            });
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
