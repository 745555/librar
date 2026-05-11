<?php

namespace Tests\Feature\Books;

use App\Livewire\Books\BookForm;
use App\Models\Department;
use App\Models\LibraryStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BookFormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quantity_must_be_positive(): void
    {
        $user = $this->createStaffUserWithPermissions(['books.create']);
        $department = Department::create(['name_ar' => 'قسم', 'name_en' => 'Dept']);

        $this->actingAs($user);

        Livewire::test(BookForm::class)
            ->set('book_title', 'Test Book')
            ->set('quantity', null)
            ->set('department_id', $department->id)
            ->call('save')
            ->assertHasErrors(['quantity']);
    }

    public function test_updating_missing_record_is_handled_safely(): void
    {
        $user = $this->createStaffUserWithPermissions(['books.edit']);
        $department = Department::create(['name_ar' => 'قسم 2', 'name_en' => 'Dept 2']);

        $this->actingAs($user);

        Livewire::test(BookForm::class)
            ->set('bookId', 999999)
            ->set('book_title', 'Ghost Book')
            ->set('quantity', 3)
            ->set('department_id', $department->id)
            ->call('save');

        $this->assertDatabaseMissing('books', [
            'book_title' => 'Ghost Book',
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
            'full_name' => 'Book Tester',
            'role' => 'staff',
        ]);

        $user->givePermissionTo($permissions);

        return $user;
    }
}
