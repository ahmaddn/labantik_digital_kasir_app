<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabantikCandidateScore extends Model
{
    use HasUuids;

    protected $table = 'labantik_candidate_scores';

    protected $fillable = [
        'registration_id',
        'week_number',
        'score',
        'attitude_score',
        'notes',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(LabantikRegistration::class, 'registration_id');
    }
}
