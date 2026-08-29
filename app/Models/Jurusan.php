<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'parent_id',
        'theme_settings',
        'pic_name',
        'phone',
        'stand_location',
        'is_active',
    ];

    protected $casts = [
        'theme_settings' => 'array',
        'is_active'      => 'boolean',
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

    /**
     * Users yang memiliki role 'pengelola' di jurusan ini.
     * Eager-load friendly — tidak ada N+1.
     *
     * Contoh penggunaan:
     *   Jurusan::with('pengelolaUsers')->get()
     *   $jurusan->pengelolaUsers->first()?->name
     */
    public function pengelolaUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'jurusan_id', 'user_id')
            ->withPivot('id', 'role_id')
            ->whereHas('roles', fn ($q) => $q->where('roles.name', 'pengelola_jurusan'))
            ->withTimestamps();
    }

    /**
     * Produk aktif milik jurusan ini.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi untuk task definitions di jurusan ini
     */
    public function taskDefinitions()
    {
        return $this->hasMany(CashierTaskDefinition::class);
    }

    /**
     * Relasi untuk task assignments di jurusan ini
     */
    public function taskAssignments()
    {
        return $this->hasMany(CashierTaskAssignment::class);
    }
}
