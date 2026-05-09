<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $updatesQueryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteProject($id)
    {
        $this->authorize('projects.delete');
        try {
            $project = Project::find($id);
            if ($project) {
                $project->delete();
                session()->flash('success', 'تم حذف المشروع بنجاح');
                $this->dispatch('swal:success', ['message' => 'تم حذف المشروع بنجاح']);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal:error', ['message' => 'حدث خطأ أثناء محاولة حذف المشروع.']);
            session()->flash('error', 'حدث خطأ أثناء محاولة حذف المشروع.');
        }
    }

    public function render()
    {
        $projects = Project::with('department')
            ->where(function($q) {
                $q->where('project_name', 'like', '%' . $this->search . '%')
                  ->orWhere('archive_number', 'like', '%' . $this->search . '%')
                  ->orWhere('student_name', 'like', '%' . $this->search . '%')
                  ->orWhere('supervisor', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(15);

        return view('livewire.projects.index', [
            'projects' => $projects
        ])->layout('layouts.app');
    }
}
