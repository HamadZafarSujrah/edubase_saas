<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrialBalance extends Component
{
    // Date Filters
    public $start_date = '';
    public $end_date   = '';

    // Column Filters
    public $filter_campus = '';

    // Summary totals
    public $total_debit  = 0;
    public $total_credit = 0;

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
     * Builds the per-account query: every active GL account for this tenant,
     * left-joined to a date/campus filtered aggregate of its journal lines so
     * that zero-activity accounts still appear with 0.00 / 0.00 instead of
     * being dropped from the report.
     */
    protected function buildAccountsQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $activitySub = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.tenant_id', $tenantId);

        if ($this->start_date) {
            $activitySub->where('je.transaction_date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $activitySub->where('je.transaction_date', '<=', $this->end_date);
        }
        if ($this->filter_campus) {
            $activitySub->where('je.campus_id', $this->filter_campus);
        }

        $activitySub->select([
            'ji.gl_account_id',
            DB::raw('SUM(ji.debit) as total_debit'),
            DB::raw('SUM(ji.credit) as total_credit'),
        ])->groupBy('ji.gl_account_id');

        return DB::table('gl_accounts as ga')
            ->leftJoinSub($activitySub, 'agg', function ($join) {
                $join->on('agg.gl_account_id', '=', 'ga.id');
            })
            ->where('ga.tenant_id', $tenantId)
            ->where('ga.is_inactive', 0)
            ->select([
                'ga.id',
                'ga.code',
                'ga.name',
                'ga.type',
                DB::raw('COALESCE(agg.total_debit, 0) as total_debit'),
                DB::raw('COALESCE(agg.total_credit, 0) as total_credit'),
            ])
            ->orderBy('ga.code');
    }

    protected function loadTotals()
    {
        $accounts = $this->buildAccountsQuery()->get();

        $this->total_debit  = (float) $accounts->sum('total_debit');
        $this->total_credit = (float) $accounts->sum('total_credit');
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

        $accounts = $this->buildAccountsQuery()->get();

        $this->total_debit  = (float) $accounts->sum('total_debit');
        $this->total_credit = (float) $accounts->sum('total_credit');

        // Group by account type, preserving the standard accounting order,
        // ordered by ga.code within each group (already sorted by the query).
        $types = ['asset', 'liability', 'equity', 'income', 'expense'];
        $grouped_accounts = [];
        foreach ($types as $type) {
            $grouped_accounts[$type] = $accounts->filter(
                fn ($a) => strtolower(trim($a->type)) === $type
            )->values();
        }

        // Allow a 0.01 floating rounding tolerance before flagging an
        // out-of-balance trial balance - this must never be hidden.
        $difference  = round($this->total_debit - $this->total_credit, 2);
        $is_balanced = abs($difference) <= 0.01;

        return view('livewire.trial-balance', [
            'grouped_accounts' => $grouped_accounts,
            'campuses'         => Campus::where('tenant_id', $tenantId)->get(),
            'difference'       => $difference,
            'is_balanced'      => $is_balanced,
        ])->layout('layouts.app');
    }
}
