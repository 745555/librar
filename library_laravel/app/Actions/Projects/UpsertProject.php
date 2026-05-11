<?php

namespace App\Actions\Projects;

use App\Models\Project;
use Illuminate\Support\Arr;

class UpsertProject
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function execute(?int $projectId, array $attributes): Project
    {
        $data = Arr::only($attributes, [
            'archive_number',
            'project_name',
            'supervisor',
            'student_name',
            'semester',
            'year',
            'department_id',
            'file_path',
            'description',
        ]);

        if ($projectId) {
            $project = Project::findOrFail($projectId);
            $project->update($data);
            return $project->refresh();
        }

        return Project::create($data);
    }
}
