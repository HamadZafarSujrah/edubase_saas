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

class AddOtherIncome extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $transaction_date;
    public $campus_id;
    public $income_account_id;
    public $receiving_account_id;
    public $amount;
    public $narration;

    public function mount()
    {
        $this->transaction_date = date('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'transaction_date'      => 'required|date',
            'income_account_id'     => 'required|exists:gl_accounts,id',
            'receiving_account_id'  => 'required|exists:gl_accounts,id|different:income_account_id',
            'amount'                => 'required|numeric|min:0.01',
            'narration'             => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'receiving_account_id.different' => 'The receiving account must be different from the income category.',
    ];

    public function save()
    {
        $this->validate();

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        DB::transaction(function () use ($tenantId) {
            $incomeAccount = GLAccount::findOrFail($this->income_account_id);
            $narration = $this->narration ?: "Other Income: {$incomeAccount->name}";
            $entry = $this->createEntryWithUniqueVoucher($tenantId, 'INC', $narration);

            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'gl_account_id'    => $this->receiving_account_id,
                'debit'            => $this->amount,
                'credit'           => 0,
                'item_memo'        => "Received: " . ($this->narration ?: $incomeAccount->name),
            ]);

            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'gl_account_id'    => $this->income_account_id,
                'debit'            => 0,
                'credit'           => $this->amount,
                'item_memo'        => $this->narration ?: "Other income recorded",
            ]);
        });

        session()->flash('message', 'Other income recorded successfully.');
        $this->reset(['campus_id', 'income_account_id', 'receiving_account_id', 'amount', 'narration']);
        $this->transaction_date = date('Y-m-d');
    }

    /**
     * Sequential per-prefix voucher numbers (INC-2026-000001, ...) race under
     * concurrent submissions -- retry on a unique-constraint collision rather
     * than surfacing a raw SQL error. Same fix as AddExpense/
     * FeeService::generateMonthlyChallan.
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

        return view('livewire.add-other-income', [
            'income_accounts'    => GLAccount::where('tenant_id', $tenantId)->where('is_inactive', 0)->where('type', 'income')->orderBy('code')->get(),
            'receiving_accounts' => GLAccount::where('tenant_id', $tenantId)->where('is_inactive', 0)->where('type', 'asset')->orderBy('code')->get(),
            'campuses'           => Campus::where('tenant_id', $tenantId)->get(),
            'recent_income'      => JournalEntry::with(['items.account', 'campus'])
                ->where('tenant_id', $tenantId)
                ->where('voucher_no', 'like', 'INC-%')
                ->latest('transaction_date')
                ->latest('id')
                ->paginate(10),
        ])->layout('layouts.app');
    }
}
