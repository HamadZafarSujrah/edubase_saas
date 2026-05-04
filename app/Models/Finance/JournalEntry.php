<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Campus\Campus;

class JournalEntry extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'campus_id', 'transaction_date', 'voucher_no', 
        'reference', 'narration', 'created_by'
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function items()
    {
        return $this->hasMany(JournalItem::class);
    }

    /**
     * Helper to get total debit of this entry
     */
    public function getTotalDebitAttribute()
    {
        return $this->items->sum('debit');
    }

    /**
     * Helper to get total credit of this entry
     */
    public function getTotalCreditAttribute()
    {
        return $this->items->sum('credit');
    }
}
