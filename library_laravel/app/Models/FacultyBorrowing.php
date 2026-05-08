<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacultyBorrowing extends Model
{
    protected $fillable = [
        'faculty_name',
        'faculty_department',
        'faculty_contact',
        'book_title',
        'book_isbn',
        'borrow_date',
        'expected_return_date',
        'actual_return_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];
}
