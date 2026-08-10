<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\Challan;
use App\Models\Campus\Campus;
use App\Models\Finance\JournalEntry;

class PaidChallans extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $campus_id = '';
    public $month = '';
    public $year = '';
    public $status = 'paid';
    // Filters by the STUDENT's active status (distinct from $status above,
    // the challan's own status). Defaults to active; switch to 'inactive' to
    // review payment history for an alumni/transferred student.
    public $student_status_filter = 'active';

    public function mount()
    {
        $this->month = strtolower(date('M'));
        $this->year = date('Y');
    }

    public function updated() { $this->resetPage(); }

    // markAsPaid() was removed: it was dead code (no button anywhere calls it,
    // and this screen's $status is hardcoded to 'paid' so it could never even
    // find an unpaid challan to act on) that reimplemented payment/GL-posting
    // outside FeeService with the same bugs as the identical copy that was in
    // ManageChallans.php. Use PayFeeChallan (which posts through
    // FeeService::collectChallanPayment) to record a payment instead.

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
