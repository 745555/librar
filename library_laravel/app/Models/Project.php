<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = [
        'archive_number',
        'project_name',
        'student_name',
        'supervisor',
        'department_id',
        'year',
        'semester',
        'project_type',
        'description',
        'file_path',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['archive_number', 'project_name', 'student_name', 'supervisor'])
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "تم {$eventName} مشروع: {$this->project_name}");
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
