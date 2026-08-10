<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Finance\GLAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashReport extends Component
{
    // Date Filters
    public $start_date = '';
    public $end_date   = '';

    // Selected cash/bank account to drill into
    public $selected_account_id = '';

    // Per-account (drill-down) figures
    public $opening_balance = 0;
    public $closing_balance = 0;
    public $total_inflow    = 0;
    public $total_outflow   = 0;
    public $daily_rows      = [];

    // Combined (all cash/bank accounts) figures for the summary cards
    public $combined_opening = 0;
    public $combined_inflow  = 0;
    public $combined_outflow = 0;
    public $combined_closing = 0;

    public function mount()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');

        $cashAccounts = $this->getCashAccounts();
        if ($cashAccounts->count() > 0) {
            $this->selected_account_id = $cashAccounts->first()->id;
        }

        $this->loadTotals();
    }

    public function updated($property)
    {
        $this->loadTotals();
    }

    public function resetFilters()
    {
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');

        $cashAccounts = $this->getCashAccounts();
        $this->selected_account_id = $cashAccounts->count() > 0 ? $cashAccounts->first()->id : '';

        $this->loadTotals();
    }

    /**
     * Cash/Bank accounts for this tenant, identified by name heuristic since
     * there is no dedicated is_cash flag on gl_accounts.
     */
    protected function getCashAccounts()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        return GLAccount::where('tenant_id', $tenantId)
            ->where('is_inactive', 0)
            ->where(function ($q) {
                $q->where('name', 'like', '%cash%')
                  ->orWhere('name', 'like', '%bank%');
            })
            ->orderBy('name')
            ->get();
    }

    /**
     * Opening balance for a single GL account as of (before) a given date.
     */
    protected function getOpeningBalance($tenantId, $accountId, $startDate)
    {
        $row = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.tenant_id', $tenantId)
            ->where('ji.gl_account_id', $accountId)
            ->where('je.transaction_date', '<', $startDate)
            ->selectRaw('COALESCE(SUM(ji.debit),0) as total_debit, COALESCE(SUM(ji.credit),0) as total_credit')
            ->first();

        return (float) ($row->total_debit ?? 0) - (float) ($row->total_credit ?? 0);
    }

    protected function loadTotals()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;
        $cashAccounts = $this->getCashAccounts();
        $cashAccountIds = $cashAccounts->pluck('id')->all();

        // Reset defaults
        $this->opening_balance = 0;
        $this->closing_balance = 0;
        $this->total_inflow    = 0;
        $this->total_outflow   = 0;
        $this->daily_rows      = [];
        $this->combined_opening = 0;
        $this->combined_inflow  = 0;
        $this->combined_outflow = 0;
        $this->combined_closing = 0;

        if (empty($cashAccountIds)) {
            return;
        }

        // ---- Combined totals across ALL cash/bank accounts for this tenant ----
        $combinedOpening = 0;
        foreach ($cashAccountIds as $accId) {
            $combinedOpening += $this->getOpeningBalance($tenantId, $accId, $this->start_date);
        }

        $combinedPeriod = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.tenant_id', $tenantId)
            ->whereIn('ji.gl_account_id', $cashAccountIds)
            ->where('je.transaction_date', '>=', $this->start_date)
            ->where('je.transaction_date', '<=', $this->end_date)
            ->selectRaw('COALESCE(SUM(ji.debit),0) as total_debit, COALESCE(SUM(ji.credit),0) as total_credit')
            ->first();

        $combinedInflow  = (float) ($combinedPeriod->total_debit ?? 0);
        $combinedOutflow = (float) ($combinedPeriod->total_credit ?? 0);

        $this->combined_opening = $combinedOpening;
        $this->combined_inflow  = $combinedInflow;
        $this->combined_outflow = $combinedOutflow;
        $this->combined_closing = $combinedOpening + $combinedInflow - $combinedOutflow;

        // ---- Per-account drill-down for the selected account ----
        if (!$this->selected_account_id || !in_array($this->selected_account_id, $cashAccountIds)) {
            return;
        }

        $this->opening_balance = $this->getOpeningBalance($tenantId, $this->selected_account_id, $this->start_date);

        $dailyTotals = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.tenant_id', $tenantId)
            ->where('ji.gl_account_id', $this->selected_account_id)
            ->where('je.transaction_date', '>=', $this->start_date)
            ->where('je.transaction_date', '<=', $this->end_date)
            ->groupBy('je.transaction_date')
            ->orderBy('je.transaction_date', 'asc')
            ->selectRaw('je.transaction_date as tx_date, COALESCE(SUM(ji.debit),0) as day_debit, COALESCE(SUM(ji.credit),0) as day_credit')
            ->get();

        $running = $this->opening_balance;
        $rows = [];
        $totalInflow  = 0;
        $totalOutflow = 0;

        foreach ($dailyTotals as $day) {
            $dayDebit  = (float) $day->day_debit;
            $dayCredit = (float) $day->day_credit;
            $running  += ($dayDebit - $dayCredit);

            $totalInflow  += $dayDebit;
            $totalOutflow += $dayCredit;

            $rows[] = [
                'date'            => $day->tx_date,
                'inflow'          => $dayDebit,
                'outflow'         => $dayCredit,
                'running_balance' => $running,
            ];
        }

        $this->daily_rows    = $rows;
        $this->total_inflow  = $totalInflow;
        $this->total_outflow = $totalOutflow;
        $this->closing_balance = count($rows) > 0 ? $running : $this->opening_balance;
    }

    public function render()
    {
        $cashAccounts = $this->getCashAccounts();

        return view('livewire.cash-report', [
            'cashAccounts' => $cashAccounts,
        ])->layout('layouts.app');
    }
}
