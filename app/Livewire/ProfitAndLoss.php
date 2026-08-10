<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfitAndLoss extends Component
{
    // Date Filters
    public $start_date = '';
    public $end_date   = '';

    // Column Filters
    public $filter_campus = '';

    // Summary totals
    public $total_income  = 0;
    public $total_expense = 0;
    public $net_profit    = 0;

    public function mount()
    {
        // Default: current month
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');

        $this->loadTotals();
    }

    public function updated()
    {
        $this->loadTotals();
    }

    /**
     * Base query joining journal_items -> journal_entries -> gl_accounts,
     * scoped to the current tenant and the selected date range / campus.
     */
    protected function buildBaseQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('gl_accounts as ga', 'ji.gl_account_id', '=', 'ga.id')
            ->leftJoin('campuses as c', 'je.campus_id', '=', 'c.id')
            ->where('je.tenant_id', $tenantId);

        // Date range
        if ($this->start_date) {
            $query->where('je.transaction_date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->where('je.transaction_date', '<=', $this->end_date);
        }

        // Campus filter
        if ($this->filter_campus) {
            $query->where('je.campus_id', $this->filter_campus);
        }

        return $query;
    }

    /**
     * Income accounts: normal credit balance, net = SUM(credit) - SUM(debit).
     * Only accounts with non-zero net activity in the range are returned.
     */
    protected function getIncomeAccounts()
    {
        return $this->buildBaseQuery()
            ->where('ga.type', 'income')
            ->select([
                'ga.id as account_id',
                'ga.code as account_code',
                'ga.name as account_name',
                DB::raw('SUM(ji.credit) - SUM(ji.debit) as net'),
            ])
            ->groupBy('ga.id', 'ga.code', 'ga.name')
            ->havingRaw('SUM(ji.credit) - SUM(ji.debit) <> 0')
            ->orderBy('ga.code')
            ->get();
    }

    /**
     * Expense accounts: normal debit balance, net = SUM(debit) - SUM(credit).
     * Only accounts with non-zero net activity in the range are returned.
     */
    protected function getExpenseAccounts()
    {
        return $this->buildBaseQuery()
            ->where('ga.type', 'expense')
            ->select([
                'ga.id as account_id',
                'ga.code as account_code',
                'ga.name as account_name',
                DB::raw('SUM(ji.debit) - SUM(ji.credit) as net'),
            ])
            ->groupBy('ga.id', 'ga.code', 'ga.name')
            ->havingRaw('SUM(ji.debit) - SUM(ji.credit) <> 0')
            ->orderBy('ga.code')
            ->get();
    }

    protected function loadTotals()
    {
        $incomeAccounts  = $this->getIncomeAccounts();
        $expenseAccounts = $this->getExpenseAccounts();

        $this->total_income  = $incomeAccounts->sum('net');
        $this->total_expense = $expenseAccounts->sum('net');
        $this->net_profit    = $this->total_income - $this->total_expense;
    }

    public function resetFilters()
    {
        $this->start_date    = date('Y-m-01');
        $this->end_date      = date('Y-m-d');
        $this->filter_campus = '';
        $this->loadTotals();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $incomeAccounts  = $this->getIncomeAccounts();
        $expenseAccounts = $this->getExpenseAccounts();

        $this->total_income  = $incomeAccounts->sum('net');
        $this->total_expense = $expenseAccounts->sum('net');
        $this->net_profit    = $this->total_income - $this->total_expense;

        return view('livewire.profit-and-loss', [
            'incomeAccounts'  => $incomeAccounts,
            'expenseAccounts' => $expenseAccounts,
            'campuses'        => Campus::where('tenant_id', $tenantId)->get(),
        ])->layout('layouts.app');
    }
}
