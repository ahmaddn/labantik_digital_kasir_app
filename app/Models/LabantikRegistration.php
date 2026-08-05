<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabantikRegistration extends Model
{
    use HasUuids;

    protected $table = 'labantik_registrations';

    protected $fillable = [
        'jurusan_id',
        'full_name',
        'class_name',
        'address',
        'phone_number',
        'parent_phone_number',
        'reason',
        'illness_history',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }
}
