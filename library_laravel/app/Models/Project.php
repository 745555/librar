<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
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

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
