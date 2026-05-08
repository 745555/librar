<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'book_title',
        'author',
        'isbn',
        'publisher',
        'publication_year',
        'quantity',
        'available_quantity',
        'department_id',
        'custom_department',
        'description',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
