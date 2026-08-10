<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\GLAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalItem;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\DB;

class JournalEntryForm extends Component
{
    public $transaction_date;
    public $campus_id;
    public $narration;
    public $items = [];
    public $total_debit = 0;
    public $total_credit = 0;

    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
        // Start with 2 empty rows like in the screenshot
        $this->addRow();
        $this->addRow();
    }

    public function addRow()
    {
        $this->items[] = [
            'gl_account_id' => '',
            'debit' => 0,
            'credit' => 0,
            'memo' => ''
        ];
    }

    public function removeRow($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total_debit = 0;
        $this->total_credit = 0;
        foreach ($this->items as $item) {
            $this->total_debit += (float) ($item['debit'] ?? 0);
            $this->total_credit += (float) ($item['credit'] ?? 0);
        }
    }

    public function save()
    {
        if ($this->total_debit != $this->total_credit || $this->total_debit == 0) {
            session()->flash('error', 'Journal Entry must be balanced (Debits must equal Credits).');
            return;
        }

        DB::transaction(function() {
            $year = date('Y', strtotime($this->transaction_date));
            $count = JournalEntry::whereYear('created_at', $year)->count() + 1;
            $voucher_no = "JV-{$year}-" . str_pad($count, 6, '0', STR_PAD_LEFT);

            $entry = JournalEntry::create([
                'tenant_id' => session('tenant_id'),
                'campus_id' => $this->campus_id,
                'transaction_date' => $this->transaction_date,
                'voucher_no' => $voucher_no,
                'narration' => $this->narration,
            ]);

            foreach ($this->items as $item) {
                if ($item['gl_account_id']) {
                    JournalItem::create([
                        'journal_entry_id' => $entry->id,
                        'gl_account_id' => $item['gl_account_id'],
                        'debit' => $item['debit'],
                        'credit' => $item['credit'],
                        'item_memo' => $item['memo']
                    ]);
                }
            }
        });

        session()->flash('message', 'Journal Entry saved successfully.');
        return redirect()->route('finance.journal-inquiry');
    }

    public function render()
    {
        return view('livewire.journal-entry-form', [
            'accounts' => GLAccount::orderBy('code')->get(),
            'campuses' => Campus::all()
        ])->layout('layouts.app');
    }
}
