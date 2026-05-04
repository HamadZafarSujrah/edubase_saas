<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;

class JournalItem extends Model
{
    protected $fillable = [
        'journal_entry_id', 'gl_account_id', 'debit', 'credit', 'item_memo'
    ];

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(GLAccount::class, 'gl_account_id');
    }
}
