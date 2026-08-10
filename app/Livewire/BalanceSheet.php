<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BalanceSheet extends Component
{
    // Filters
    public $as_of_date = '';
    public $filter_campus = '';

    // Section data
    public $assets = [];
    public $liabilities = [];
    public $equity = [];

    // Totals
    public $total_assets = 0;
    public $total_liabilities = 0;
    public $total_equity_accounts = 0;
    public $net_income = 0;
    public $total_equity = 0;
    public $total_liabilities_and_equity = 0;
    public $is_balanced = true;
    public $difference = 0;

    public function mount()
    {
        $this->as_of_date = date('Y-m-d');
        $this->loadTotals();
    }

    public function updated()
    {
        $this->loadTotals();
    }

    /**
     * Base query: journal_items joined to journal_entries + gl_accounts,
     * tenant-scoped, bounded by transaction_date <= as_of_date (cumulative
     * from inception, no lower bound) and optional campus filter.
     */
    protected function buildBaseQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('gl_accounts as ga', 'ji.gl_account_id', '=', 'ga.id')
            ->leftJoin('campuses as c', 'je.campus_id', '=', 'c.id')
            ->where('je.tenant_id', $tenantId);

        if ($this->as_of_date) {
            $query->where('je.transaction_date', '<=', $this->as_of_date);
        }

        if ($this->filter_campus) {
            $query->where('je.campus_id', $this->filter_campus);
        }

        return $query;
    }

    /**
     * Per-account balances for a given GL account type, using the supplied
     * balance formula (debit-normal for assets, credit-normal for
     * liability/equity/income, debit-normal for expense).
     */
    protected function getAccountBalances(string $type, bool $creditNormal)
    {
        $balanceExpr = $creditNormal
            ? 'SUM(ji.credit) - SUM(ji.debit)'
            : 'SUM(ji.debit) - SUM(ji.credit)';

        $rows = $this->buildBaseQuery()
            ->where('ga.type', $type)
            ->groupBy('ga.id', 'ga.code', 'ga.name')
            ->orderBy('ga.code')
            ->select([
                'ga.id',
                'ga.code',
                'ga.name',
                DB::raw("$balanceExpr as balance"),
            ])
            ->get();

        // Only show accounts with a non-zero cumulative balance
        return $rows->filter(function ($row) {
            return round((float) $row->balance, 2) != 0.00;
        })->values();
    }

    protected function loadTotals()
    {
        // Asset accounts: debit-normal
        $this->assets = $this->getAccountBalances('asset', false);
        $this->total_assets = $this->assets->sum('balance');

        // Liability accounts: credit-normal
        $this->liabilities = $this->getAccountBalances('liability', true);
        $this->total_liabilities = $this->liabilities->sum('balance');

        // Equity accounts: credit-normal
        $this->equity = $this->getAccountBalances('equity', true);
        $this->total_equity_accounts = $this->equity->sum('balance');

        // Current Period Profit/(Loss): no closing process exists, so we
        // compute income - expense ourselves for display under Equity.
        $incomeTotal = (float) $this->buildBaseQuery()
            ->where('ga.type', 'income')
            ->selectRaw('COALESCE(SUM(ji.credit) - SUM(ji.debit), 0) as total')
            ->value('total');

        $expenseTotal = (float) $this->buildBaseQuery()
            ->where('ga.type', 'expense')
            ->selectRaw('COALESCE(SUM(ji.debit) - SUM(ji.credit), 0) as total')
            ->value('total');

        $this->net_income = $incomeTotal - $expenseTotal;

        // Totals
        $this->total_equity = $this->total_equity_accounts + $this->net_income;
        $this->total_liabilities_and_equity = $this->total_liabilities + $this->total_equity;

        $this->difference = round($this->total_assets - $this->total_liabilities_and_equity, 2);
        $this->is_balanced = abs($this->difference) <= 0.01;
    }

    public function resetFilters()
    {
        $this->as_of_date = date('Y-m-d');
        $this->filter_campus = '';
        $this->loadTotals();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return view('livewire.balance-sheet', [
            'campuses' => Campus::where('tenant_id', $tenantId)->get(),
        ])->layout('layouts.app');
    }
}
