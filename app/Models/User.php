<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password','active_session_id','type','permissions'
    ];
public function session(){
    return $this->belongsTo(Session::class,'active_session_id','id');
}
    public function profile(){
    return $this->belongsTo(Profile::class,'id','user_id');
}

    /**
     * Roles are the source of access for normal users. The legacy
     * `permissions` column is retained only so existing installations can be
     * migrated without losing access.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (User $user) {
            if ($user->isAdmin() || $user->isSuperAdmin()) {
                throw new \RuntimeException('Super Administrator accounts cannot be deleted.');
            }
        });
    }

    public function isAdmin(): bool
    {
        return $this->type === 'admin' || $this->type === 'super_admin';
    }

    public function isSuperAdmin(): bool
    {
        return $this->isAdmin();
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if (! $this->relationLoaded('roles')) {
            $this->load('roles.permissions');
        }

        $roles = $this->roles;

        return $roles->contains(function ($role) use ($permission) {
            $permissions = $role->relationLoaded('permissions')
                ? $role->permissions
                : $role->permissions()->get();

            return $permissions->contains('slug', $permission);
        });
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
