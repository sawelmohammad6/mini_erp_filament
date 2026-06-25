<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('Admin');

        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
        ]);
        $manager->assignRole('Manager');

        $staff = User::factory()->create([
            'name' => 'Staff',
            'email' => 'staff@example.com',
        ]);
        $staff->assignRole('Staff');
    }
}
