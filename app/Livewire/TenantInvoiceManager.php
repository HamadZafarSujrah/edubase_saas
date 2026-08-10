<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\TenantInvoice;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TenantInvoiceManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $tenant_filter = '';
    public $status_filter = '';

    public $isModalOpen = false;
    public $tenant_id, $plan_id, $amount, $billing_period_start, $billing_period_end, $due_date, $notes;

    public $isPayModalOpen = false;
    public $payingInvoiceId;
    public $payment_method = 'bank_transfer';

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin() || $user->tenant_id) {
            abort(403, 'Only platform-level super admins can manage billing.');
        }
    }

    public function render()
    {
        $query = TenantInvoice::with(['tenant', 'plan']);

        if ($this->tenant_filter) {
            $query->where('tenant_id', $this->tenant_filter);
        }
        if ($this->status_filter) {
            $query->where('status', $this->status_filter);
        }

        return view('livewire.tenant-invoice-manager', [
            'invoices' => $query->latest('billing_period_start')->paginate(15),
            'tenants' => Tenant::orderBy('name')->get(),
            'plans' => Plan::where('is_active', true)->orderBy('price')->get(),
        ])->layout('layouts.app');
    }

    public function openModal()
    {
        $this->billing_period_start = now()->startOfMonth()->format('Y-m-d');
        $this->billing_period_end = now()->endOfMonth()->format('Y-m-d');
        $this->due_date = now()->addDays(7)->format('Y-m-d');
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->reset(['tenant_id', 'plan_id', 'amount', 'billing_period_start', 'billing_period_end', 'due_date', 'notes']);
    }

    public function updatedTenantId()
    {
        $tenant = Tenant::find($this->tenant_id);
        if ($tenant && $tenant->plan) {
            $this->plan_id = $tenant->plan_id;
            $this->amount = $tenant->plan->price;
        }
    }

    public function save()
    {
        $this->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan_id' => 'nullable|exists:plans,id',
            'amount' => 'required|numeric|min:0',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
            'due_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $exists = TenantInvoice::where('tenant_id', $this->tenant_id)
            ->where('billing_period_start', $this->billing_period_start)
            ->where('billing_period_end', $this->billing_period_end)
            ->exists();

        if ($exists) {
            $this->addError('billing_period_start', 'An invoice already exists for this tenant covering this exact billing period.');
            return;
        }

        TenantInvoice::create([
            'tenant_id' => $this->tenant_id,
            'plan_id' => $this->plan_id ?: null,
            'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . str_pad($this->tenant_id, 4, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999),
            'amount' => $this->amount,
            'billing_period_start' => $this->billing_period_start,
            'billing_period_end' => $this->billing_period_end,
            'due_date' => $this->due_date,
            'status' => 'unpaid',
            'notes' => $this->notes,
        ]);

        session()->flash('message', 'Invoice created successfully.');
        $this->closeModal();
    }

    /**
     * Idempotent, mirrors GenerateChallan's skip-if-exists pattern: one invoice
     * per tenant per calendar month, skipped (not errored) if already generated.
     */
    public function generateMonthlyInvoices()
    {
        $periodStart = now()->startOfMonth()->format('Y-m-d');
        $periodEnd = now()->endOfMonth()->format('Y-m-d');
        $generated = 0;

        foreach (Tenant::where('status', 'active')->whereNotNull('plan_id')->with('plan')->get() as $tenant) {
            if (!$tenant->plan || $tenant->plan->price <= 0) {
                continue;
            }

            $exists = TenantInvoice::where('tenant_id', $tenant->id)
                ->where('billing_period_start', $periodStart)
                ->where('billing_period_end', $periodEnd)
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                TenantInvoice::create([
                    'tenant_id' => $tenant->id,
                    'plan_id' => $tenant->plan_id,
                    'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . str_pad($tenant->id, 4, '0', STR_PAD_LEFT) . '-' . rand(1000, 9999),
                    'amount' => $tenant->plan->price,
                    'billing_period_start' => $periodStart,
                    'billing_period_end' => $periodEnd,
                    'due_date' => now()->addDays(7)->format('Y-m-d'),
                    'status' => 'unpaid',
                ]);
                $generated++;
            } catch (\Illuminate\Database\QueryException $e) {
                // Unique-period race with another request -- already handled, skip.
                continue;
            }
        }

        session()->flash('message', "Generated {$generated} invoice(s) for " . now()->format('F Y') . '.');
    }

    public function openPayModal($id)
    {
        $this->payingInvoiceId = $id;
        $this->payment_method = 'bank_transfer';
        $this->isPayModalOpen = true;
    }

    public function closePayModal()
    {
        $this->isPayModalOpen = false;
        $this->payingInvoiceId = null;
    }

    public function markPaid()
    {
        $this->validate(['payment_method' => 'required|string|max:50']);

        $invoice = TenantInvoice::findOrFail($this->payingInvoiceId);
        $invoice->update([
            'status' => 'paid',
            'payment_method' => $this->payment_method,
            'paid_at' => now(),
            'paid_by' => Auth::id(),
        ]);

        session()->flash('message', 'Invoice marked as paid.');
        $this->closePayModal();
    }

    public function voidInvoice($id)
    {
        $invoice = TenantInvoice::findOrFail($id);
        $invoice->update(['status' => 'void']);
        session()->flash('message', 'Invoice voided.');
    }
}
