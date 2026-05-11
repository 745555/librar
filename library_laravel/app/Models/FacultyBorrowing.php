<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class FacultyBorrowing extends Model
{
    use LogsActivity, SoftDeletes;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['faculty_name', 'book_title', 'status', 'expected_return_date', 'actual_return_date'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} إعارة: {$this->book_title} - {$this->faculty_name}");
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
