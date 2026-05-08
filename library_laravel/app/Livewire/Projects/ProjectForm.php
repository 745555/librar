<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use App\Models\Department;
use Livewire\Component;

class ProjectForm extends Component
{
    public $projectId = null;
    public $archive_number;
    public $project_name;
    public $supervisor;
    public $student_name;
    public $semester;
    public $year;
    public $department_id;
    public $file_path;
    public $description;

    public function mount($id = null)
    {
        if ($id) {
            $this->projectId = $id;
            $project = Project::findOrFail($id);
            $this->archive_number = $project->archive_number;
            $this->project_name = $project->project_name;
            $this->supervisor = $project->supervisor;
            $this->student_name = $project->student_name;
            $this->semester = $project->semester;
            $this->year = $project->year;
            $this->department_id = $project->department_id;
            $this->file_path = $project->file_path;
            $this->description = $project->description;
        } else {
            $this->year = date('Y');
        }
    }

    protected function rules()
    {
        return [
            'archive_number' => 'required|string|max:50|unique:projects,archive_number,' . $this->projectId,
            'project_name' => 'required|string|max:255',
            'supervisor' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
            'semester' => 'required|string',
            'year' => 'required|integer',
            'department_id' => 'required|exists:departments,id',
            'file_path' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'archive_number' => $this->archive_number,
            'project_name' => $this->project_name,
            'supervisor' => $this->supervisor,
            'student_name' => $this->student_name,
            'semester' => $this->semester,
            'year' => $this->year,
            'department_id' => $this->department_id,
            'file_path' => $this->file_path,
            'description' => $this->description,
        ];

        if ($this->projectId) {
            Project::find($this->projectId)->update($data);
            session()->flash('success', 'تم تحديث المشروع بنجاح');
        } else {
            Project::create($data);
            session()->flash('success', 'تم إضافة المشروع بنجاح');
        }

        return redirect()->route('projects.index');
    }

    public function render()
    {
        return view('livewire.projects.form', [
            'departments' => Department::all()
        ])->layout('layouts.app');
    }
}
