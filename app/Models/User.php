<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'points', 'pending_points', 'streak'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUuids, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * User roles and their associated jurusans
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot('id', 'jurusan_id')
            ->withTimestamps();
    }

    /**
     * Get user accesses (role + jurusan context)
     */
    public function getAvailableAccesses()
    {
        if ($this->relationLoaded('available_accesses')) {
            return $this->getRelation('available_accesses');
        }

        return \DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->leftJoin('jurusans', 'role_user.jurusan_id', '=', 'jurusans.id')
            ->where('role_user.user_id', $this->id)
            ->select(
                'role_user.id as access_id',
                'roles.id as role_id',
                'roles.name as role_name',
                'roles.label as role_label',
                'jurusans.id as jurusan_id',
                'jurusans.name as jurusan_name'
            )
            ->get();
    }
}
