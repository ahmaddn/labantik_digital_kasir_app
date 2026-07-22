<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierNote extends Model
{
    protected $fillable = [
        'jurusan_id',
        'user_id',
        'target_user_id',
        'title',
        'content',
        'color',
        'is_pinned',
        'date',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function replies()
    {
        return $this->hasMany(NoteReply::class, 'cashier_note_id')->orderBy('created_at', 'asc');
    }
}
