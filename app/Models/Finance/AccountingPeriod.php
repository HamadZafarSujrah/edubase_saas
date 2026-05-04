<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\User;

class AccountingPeriod extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'start_date', 'end_date', 'is_closed', 'created_by', 'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for open periods
     */
    public function scopeOpen($query)
    {
        return $query->where('is_closed', false);
    }
}
