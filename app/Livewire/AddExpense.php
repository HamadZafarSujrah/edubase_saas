<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\GLAccount;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\JournalItem;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AddExpense extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $transaction_date;
    public $campus_id;
    public $expense_account_id;
    public $paying_account_id;
    public $amount;
    public $narration;

    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'transaction_date'   => 'required|date',
            'expense_account_id' => 'required|exists:gl_accounts,id',
            'paying_account_id'  => 'required|exists:gl_accounts,id|different:expense_account_id',
            'amount'             => 'required|numeric|min:0.01',
            'narration'          => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'paying_account_id.different' => 'The paying account must be different from the expense account.',
    ];

    public function save()
    {
        $this->validate();

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        DB::transaction(function () use ($tenantId) {
            $expenseAccount = GLAccount::findOrFail($this->expense_account_id);
            $narration = $this->narration ?: "Expense: {$expenseAccount->name}";
            $entry = $this->createEntryWithUniqueVoucher($tenantId, 'EXP', $narration);

            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'gl_account_id'    => $this->expense_account_id,
                'debit'            => $this->amount,
                'credit'           => 0,
                'item_memo'        => $this->narration ?: "Expense recorded",
            ]);

            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'gl_account_id'    => $this->paying_account_id,
                'debit'            => 0,
                'credit'           => $this->amount,
                'item_memo'        => "Payment for: " . ($this->narration ?: $expenseAccount->name),
            ]);
        });

        session()->flash('message', 'Expense recorded successfully.');
        $this->reset(['campus_id', 'expense_account_id', 'paying_account_id', 'amount', 'narration']);
        $this->transaction_date = date('Y-m-d');
    }

    /**
     * Sequential per-prefix voucher numbers (EXP-2026-000001, ...) race under
     * concurrent submissions -- count()+1 read by two requests before either
     * commits produces the same number, and voucher_no has a unique
     * constraint. Retry with the next number on collision instead of
     * surfacing a raw SQL error (same class of bug fixed in
     * FeeService::generateMonthlyChallan's challan_no generation).
     */
    protected function createEntryWithUniqueVoucher(int $tenantId, string $prefix, string $narration): JournalEntry
    {
        $year = date('Y', strtotime($this->transaction_date));

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $count = JournalEntry::where('voucher_no', 'like', "{$prefix}-{$year}-%")->count() + 1 + $attempt;
            $voucherNo = "{$prefix}-{$year}-" . str_pad($count, 6, '0', STR_PAD_LEFT);

            try {
                return JournalEntry::create([
                    'tenant_id'        => $tenantId,
                    'campus_id'        => $this->campus_id ?: null,
                    'transaction_date' => $this->transaction_date,
                    'voucher_no'       => $voucherNo,
                    'narration'        => $narration,
                    'created_by'       => Auth::id(),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($attempt === 4) {
                    throw $e;
                }
            }
        }
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.add-expense', [
            'expense_accounts' => GLAccount::where('tenant_id', $tenantId)->where('is_inactive', 0)->where('type', 'expense')->orderBy('code')->get(),
            'paying_accounts'  => GLAccount::where('tenant_id', $tenantId)->where('is_inactive', 0)->where('type', 'asset')->orderBy('code')->get(),
            'campuses'         => Campus::where('tenant_id', $tenantId)->get(),
            'recent_expenses'  => JournalEntry::with(['items.account', 'campus'])
                ->where('tenant_id', $tenantId)
                ->where('voucher_no', 'like', 'EXP-%')
                ->latest('transaction_date')
                ->latest('id')
                ->paginate(10),
        ])->layout('layouts.app');
    }
}
