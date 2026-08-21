<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabantikCandidateAttendance extends Model
{
    use HasUuids;

    protected $table = 'labantik_candidate_attendances';

    protected $fillable = [
        'registration_id',
        'week_number',
        'status',
        'reason',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(LabantikRegistration::class, 'registration_id');
    }
}
