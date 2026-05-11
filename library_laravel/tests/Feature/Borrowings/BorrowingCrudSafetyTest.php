<?php

namespace Tests\Feature\Borrowings;

use App\Livewire\Borrowings\Index;
use App\Models\LibraryStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class BorrowingCrudSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_borrowing(): void
    {
        $user = $this->createStaffUserWithPermissions(['borrowings.create', 'borrowings.view']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('faculty_name', 'Faculty A')
            ->set('faculty_department', 'CS')
            ->set('book_title', 'Book A')
            ->set('borrow_date', '2026-05-10')
            ->set('expected_return_date', '2026-05-20')
            ->call('save');

        $this->assertDatabaseHas('faculty_borrowings', [
            'faculty_name' => 'Faculty A',
            'book_title' => 'Book A',
        ]);
    }

    public function test_missing_borrowing_record_is_handled_safely(): void
    {
        $user = $this->createStaffUserWithPermissions(['borrowings.edit', 'borrowings.view']);

        $this->actingAs($user);

        Livewire::test(Index::class)
            ->set('borrowId', 999999)
            ->set('faculty_name', 'Faculty B')
            ->set('faculty_department', 'IT')
            ->set('book_title', 'Book B')
            ->set('borrow_date', '2026-05-10')
            ->set('expected_return_date', '2026-05-20')
            ->call('save')
            ->assertDispatched('swal:error');
    }

    private function createStaffUserWithPermissions(array $permissions): LibraryStaff
    {
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $user = LibraryStaff::create([
            'username' => 'user_' . uniqid(),
            'password' => Hash::make('password123'),
            'full_name' => 'Borrowing Tester',
            'role' => 'staff',
        ]);

        $user->givePermissionTo($permissions);

        return $user;
    }
}
