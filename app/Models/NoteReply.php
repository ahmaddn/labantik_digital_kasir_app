<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteReply extends Model
{
    protected $fillable = [
        'cashier_note_id',
        'user_id',
        'content',
    ];

    public function note()
    {
        return $this->belongsTo(CashierNote::class, 'cashier_note_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
