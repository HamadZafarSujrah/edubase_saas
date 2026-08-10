<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantInvoice extends Model
{
    // Deliberately no HasTenant -- see the migration's comment. This is
    // platform-billing data the Super Admin Portal must see across every
    // tenant; tenant-side queries filter by tenant_id explicitly instead.
    protected $fillable = [
        'tenant_id', 'plan_id', 'invoice_no', 'amount', 'billing_period_start', 'billing_period_end',
        'due_date', 'status', 'payment_method', 'paid_at', 'paid_by', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_period_start' => 'date',
        'billing_period_end' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
