<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\JournalItem;
use App\Models\Finance\JournalEntry;
use App\Models\Finance\GLAccount;
use App\Models\Campus\Campus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GeneralLedger extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Date Filters
    public $start_date = '';
    public $end_date   = '';

    // Column Filters
    public $filter_campus    = '';
    public $filter_trans_id  = '';
    public $filter_account   = '';
    public $filter_dr_cr     = '';
    public $filter_amount    = '';

    // Summary totals
    public $total_debit  = 0;
    public $total_credit = 0;
    public $total_count  = 0;

    public function mount()
    {
        // Default: current month
        $this->start_date = date('Y-m-01');
        $this->end_date   = date('Y-m-d');

        $this->loadTotals();
    }

    public function updated()
    {
        $this->resetPage();
        $this->loadTotals();
    }

    protected function loadTotals()
    {
        $query = $this->buildQuery();
        $this->total_count  = $query->count();
        $this->total_debit  = $query->sum('ji.debit');
        $this->total_credit = $query->sum('ji.credit');
    }

    protected function buildQuery()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $query = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('gl_accounts as ga', 'ji.gl_account_id', '=', 'ga.id')
            ->leftJoin('campuses as c', 'je.campus_id', '=', 'c.id')
            ->leftJoin('users as u', 'je.created_by', '=', 'u.id')
            ->where('je.tenant_id', $tenantId)
            ->select([
                'ji.id',
                'ji.journal_entry_id',
                'ji.gl_account_id',
                'ji.debit',
                'ji.credit',
                'ji.item_memo',
                'ji.created_at',
                'ji.updated_at',
                'je.voucher_no',
                'je.transaction_date',
                'je.narration',
                'je.campus_id',
                'ga.code as account_code',
                'ga.name as account_name',
                'ga.type as account_type',
                'c.name as campus_name',
                'u.name as created_by_name',
            ]);

        // Date range
        if ($this->start_date) {
            $query->where('je.transaction_date', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->where('je.transaction_date', '<=', $this->end_date);
        }

        // Column filters
        if ($this->filter_campus) {
            $query->where('je.campus_id', $this->filter_campus);
        }
        if ($this->filter_trans_id) {
            $query->where('je.voucher_no', 'like', '%' . $this->filter_trans_id . '%');
        }
        if ($this->filter_account) {
            $query->where(function ($q) {
                $q->where('ga.name', 'like', '%' . $this->filter_account . '%')
                  ->orWhere('ga.code', 'like', '%' . $this->filter_account . '%');
            });
        }
        if ($this->filter_dr_cr === 'dr') {
            $query->where('ji.debit', '>', 0);
        } elseif ($this->filter_dr_cr === 'cr') {
            $query->where('ji.credit', '>', 0);
        }
        if ($this->filter_amount) {
            $query->where(function ($q) {
                $q->where('ji.debit', 'like', '%' . $this->filter_amount . '%')
                  ->orWhere('ji.credit', 'like', '%' . $this->filter_amount . '%');
            });
        }

        return $query->orderBy('je.transaction_date', 'desc')->orderBy('ji.id', 'desc');
    }

    public function resetFilters()
    {
        $this->start_date    = date('Y-m-01');
        $this->end_date      = date('Y-m-d');
        $this->filter_campus  = '';
        $this->filter_trans_id = '';
        $this->filter_account  = '';
        $this->filter_dr_cr    = '';
        $this->filter_amount   = '';
        $this->resetPage();
        $this->loadTotals();
    }

    public function render()
    {
        $tenantId = session('tenant_id') ?? Auth::user()->tenant_id;

        $entries = $this->buildQuery()->paginate(50);

        // Row-level line item numbers within each entry
        $lineNumbers = [];
        $entryCounts = [];
        foreach ($entries as $row) {
            if (!isset($entryCounts[$row->journal_entry_id])) {
                $entryCounts[$row->journal_entry_id] = 1;
            }
            $lineNumbers[$row->id] = $entryCounts[$row->journal_entry_id]++;
        }

        return view('livewire.general-ledger', [
            'entries'      => $entries,
            'lineNumbers'  => $lineNumbers,
            'campuses'     => Campus::where('tenant_id', $tenantId)->get(),
            'gl_accounts'  => GLAccount::where('tenant_id', $tenantId)->get(),
        ])->layout('layouts.app');
    }
}
