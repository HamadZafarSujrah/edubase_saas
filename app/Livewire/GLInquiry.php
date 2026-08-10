<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\GLAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GLInquiry extends Component
{
    // Filters
    public $account_id = '';
    public $start_date = '';
    public $end_date   = '';

    // Computed
    public $opening_balance = 0;
    public $closing_balance = 0;
    public $total_debit     = 0;
    public $total_credit    = 0;
    public $rows            = [];

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $first = GLAccount::where('tenant_id', $tenantId)->where('is_inactive', 0)->orderBy('code')->first();
        if ($first) {
            $this->account_id = $first->id;
        }

        $this->loadLedger();
    }

    public function updated() { $this->loadLedger(); }

    public function resetFilters()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');
        $this->loadLedger();
    }

    /**
     * Asset/expense accounts are debit-normal (balance = debit - credit);
     * liability/equity/income accounts are credit-normal (balance = credit -
     * debit). Same convention used in BalanceSheet/TrialBalance.
     */
    protected function isCreditNormal(string $type): bool
    {
        return in_array($type, ['liability', 'equity', 'income']);
    }

    protected function loadLedger()
    {
        $this->opening_balance = 0;
        $this->closing_balance = 0;
        $this->total_debit     = 0;
        $this->total_credit    = 0;
        $this->rows            = [];

        if (!$this->account_id) {
            return;
        }

        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $account  = GLAccount::where('tenant_id', $tenantId)->find($this->account_id);
        if (!$account) {
            return;
        }

        $creditNormal = $this->isCreditNormal($account->type);

        $openingRow = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.tenant_id', $tenantId)
            ->where('ji.gl_account_id', $account->id)
            ->where('je.transaction_date', '<', $this->start_date)
            ->selectRaw('COALESCE(SUM(ji.debit),0) as total_debit, COALESCE(SUM(ji.credit),0) as total_credit')
            ->first();

        $openingDebit  = (float) ($openingRow->total_debit ?? 0);
        $openingCredit = (float) ($openingRow->total_credit ?? 0);
        $this->opening_balance = $creditNormal ? ($openingCredit - $openingDebit) : ($openingDebit - $openingCredit);

        $transactions = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.tenant_id', $tenantId)
            ->where('ji.gl_account_id', $account->id)
            ->where('je.transaction_date', '>=', $this->start_date)
            ->where('je.transaction_date', '<=', $this->end_date)
            ->orderBy('je.transaction_date', 'asc')
            ->orderBy('ji.id', 'asc')
            ->select(['ji.debit', 'ji.credit', 'ji.item_memo', 'je.transaction_date', 'je.voucher_no', 'je.narration'])
            ->get();

        $running = $this->opening_balance;
        $rows = [];
        $totalDebit  = 0;
        $totalCredit = 0;

        foreach ($transactions as $t) {
            $debit  = (float) $t->debit;
            $credit = (float) $t->credit;
            $running += $creditNormal ? ($credit - $debit) : ($debit - $credit);
            $totalDebit  += $debit;
            $totalCredit += $credit;

            $rows[] = [
                'date'       => $t->transaction_date,
                'voucher_no' => $t->voucher_no,
                'memo'       => $t->item_memo ?: $t->narration,
                'debit'      => $debit,
                'credit'     => $credit,
                'balance'    => $running,
            ];
        }

        $this->rows            = $rows;
        $this->total_debit     = $totalDebit;
        $this->total_credit    = $totalCredit;
        $this->closing_balance = $running;
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.gl-inquiry', [
            'accounts' => GLAccount::where('tenant_id', $tenantId)->where('is_inactive', 0)->orderBy('code')->get(),
            'selectedAccount' => $this->account_id ? GLAccount::find($this->account_id) : null,
        ])->layout('layouts.app');
    }
}
