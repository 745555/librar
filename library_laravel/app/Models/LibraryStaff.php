<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LibraryStaff extends Authenticatable
{
    use Notifiable;

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
