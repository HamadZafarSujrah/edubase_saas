<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasTenant;
use App\Models\Campus\Campus;
use App\Models\User;

class Notice extends Model
{
    use HasTenant;

    protected $fillable = [
        'tenant_id', 'campus_id', 'title', 'body', 'published_by', 'published_at', 'expiry_date', 'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
