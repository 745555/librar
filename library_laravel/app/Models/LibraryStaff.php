<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LibraryStaff extends Authenticatable
{
    use Notifiable, HasRoles, SoftDeletes, LogsActivity;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['username', 'full_name', 'email', 'role'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} موظف: {$this->full_name}");
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
