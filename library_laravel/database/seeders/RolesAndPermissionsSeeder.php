<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Books
            'books.view',
            'books.create',
            'books.edit',
            'books.delete',
            'books.manage',
            
            // Projects
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'projects.manage',
            
            // Staff
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'staff.manage',
            
            // Borrowings
            'borrowings.view',
            'borrowings.create',
            'borrowings.edit',
            'borrowings.delete',
            'borrowings.manage',
            
            // System
            'system.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions
        
        // Admin: Has everything (handled by Gate::before, but good to have the role)
        $adminRole = Role::create(['name' => 'Admin']);

        // Librarian: Manage books and borrowings
        $librarianRole = Role::create(['name' => 'Librarian']);
        $librarianRole->givePermissionTo([
            'books.view', 'books.create', 'books.edit', 'books.delete',
            'projects.view', 'projects.create', 'projects.edit',
            'borrowings.view', 'borrowings.manage'
        ]);

        // Staff: View only and simple entries
        $staffRole = Role::create(['name' => 'Staff']);
        $staffRole->givePermissionTo([
            'books.view',
            'projects.view',
            'borrowings.view'
        ]);
    }
}
