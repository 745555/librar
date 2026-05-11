<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Book extends Model
{
    use LogsActivity, SoftDeletes;

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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['book_title', 'author', 'isbn', 'quantity', 'available_quantity'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} كتاب: {$this->book_title}");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function borrowings()
    {
        return $this->hasMany(FacultyBorrowing::class, 'book_title', 'book_title');
    }
}
