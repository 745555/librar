<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class LibraryStaff extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $table = 'library_staff';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'email',
        'phone',
        'role',
        'avatar',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
