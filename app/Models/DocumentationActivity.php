<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'jurusan_id',
        'title',
        'start_date',
        'end_date',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'jurusan_id');
    }

    public function schedules()
    {
        return $this->hasMany(DocumentationSchedule::class, 'activity_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
