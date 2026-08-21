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
        'is_joined_group',
        'is_accepted',
    ];

    protected $casts = [
        'is_joined_group' => 'boolean',
        'is_accepted' => 'boolean',
    ];

    public function jurusan(): BelongsTo
    {
        return $this->belongsTo(Jurusan::class);
    }

    public function scores()
    {
        return $this->hasMany(LabantikCandidateScore::class, 'registration_id');
    }

    public function attendances()
    {
        return $this->hasMany(LabantikCandidateAttendance::class, 'registration_id');
    }
}
