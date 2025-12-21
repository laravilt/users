<?php

namespace Laravilt\Users\Tests\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravilt\Users\Concerns\HasRolesAndPermissions;
use Laravilt\Users\Tests\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;
    use HasRolesAndPermissions;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
