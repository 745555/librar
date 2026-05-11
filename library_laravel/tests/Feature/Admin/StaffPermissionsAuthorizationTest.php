<?php

namespace Tests\Feature\Admin;

use App\Models\LibraryStaff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StaffPermissionsAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_privileged_user_cannot_open_staff_permissions_page(): void
    {
        $user = $this->createStaffUser();

        $this->actingAs($user)
            ->get('/staff/permissions')
            ->assertForbidden();
    }

    public function test_privileged_user_can_open_staff_permissions_page(): void
    {
        Permission::findOrCreate('system.manage', 'web');
        $user = $this->createStaffUser();
        $user->givePermissionTo('system.manage');

        $this->actingAs($user)
            ->get('/staff/permissions')
            ->assertOk();
    }

    private function createStaffUser(array $attributes = []): LibraryStaff
    {
        $defaults = [
            'username' => 'user_' . uniqid(),
            'password' => Hash::make('password123'),
            'full_name' => 'Test User',
            'email' => null,
            'phone' => null,
            'role' => 'staff',
        ];

        return LibraryStaff::create(array_merge($defaults, $attributes));
    }
}
