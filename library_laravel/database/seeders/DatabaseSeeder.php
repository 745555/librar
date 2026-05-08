<?php

namespace Database\Seeders;

use App\Models\LibraryStaff;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin
        LibraryStaff::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'full_name' => 'مدير النظام',
            'email' => 'admin@library.com',
            'role' => 'admin',
        ]);

        // Departments
        $departments = [
            ['name_ar' => 'قسم الحاسب الآلي', 'name_en' => 'Computer Science'],
            ['name_ar' => 'قسم الهندسة المدنية', 'name_en' => 'Civil Engineering'],
            ['name_ar' => 'قسم الهندسة الكهربائية', 'name_en' => 'Electrical Engineering'],
            ['name_ar' => 'قسم الهندسة الميكانيكية', 'name_en' => 'Mechanical Engineering'],
            ['name_ar' => 'قسم إدارة الأعمال', 'name_en' => 'Business Administration'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
