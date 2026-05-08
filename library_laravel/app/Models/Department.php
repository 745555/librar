<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'description',
    ];

    public $timestamps = false; // Legacy table uses created_at only

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
