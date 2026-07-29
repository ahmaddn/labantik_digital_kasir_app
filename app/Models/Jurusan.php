<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jurusan extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'parent_id',
        'theme_settings',
    ];

    protected $casts = [
        'theme_settings' => 'array',
    ];

    public function parent()
    {
        return $this->belongsTo(Jurusan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Jurusan::class, 'parent_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withPivot('id', 'role_id')
            ->withTimestamps();
    }
}
