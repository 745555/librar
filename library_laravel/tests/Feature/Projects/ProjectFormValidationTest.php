<?php

namespace Tests\Feature\Projects;

use App\Livewire\Projects\ProjectForm;
use App\Models\Department;
use App\Models\LibraryStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProjectFormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_is_required(): void
    {
        $user = $this->createStaffUserWithPermissions(['projects.create']);

        $this->actingAs($user);

        Livewire::test(ProjectForm::class)
            ->set('archive_number', 'ARCH-001')
            ->set('project_name', 'Project A')
            ->set('supervisor', 'Supervisor A')
            ->set('student_name', 'Student A')
            ->set('semester', 'First')
            ->set('year', 2026)
            ->set('department_id', null)
            ->call('save')
            ->assertHasErrors(['department_id']);
    }

    public function test_updating_missing_project_is_handled_safely(): void
    {
        $user = $this->createStaffUserWithPermissions(['projects.edit']);
        $department = Department::create(['name_ar' => 'قسم مشاريع', 'name_en' => 'Projects Dept']);

        $this->actingAs($user);

        Livewire::test(ProjectForm::class)
            ->set('projectId', 999999)
            ->set('archive_number', 'ARCH-404')
            ->set('project_name', 'Ghost Project')
            ->set('supervisor', 'Supervisor')
            ->set('student_name', 'Student')
            ->set('semester', 'Second')
            ->set('year', 2026)
            ->set('department_id', $department->id)
            ->call('save');

        $this->assertDatabaseMissing('projects', [
            'archive_number' => 'ARCH-404',
        ]);
    }

    private function createStaffUserWithPermissions(array $permissions): LibraryStaff
    {
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $user = LibraryStaff::create([
            'username' => 'user_' . uniqid(),
            'password' => Hash::make('password123'),
            'full_name' => 'Project Tester',
            'role' => 'staff',
        ]);

        $user->givePermissionTo($permissions);

        return $user;
    }
}
