<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        if (!User::where('email', 'admin@example.com')->exists()) {
            $admin = User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
            ]);
            $admin->assignRole('Admin');
        }

        if (!User::where('email', 'manager@example.com')->exists()) {
            $manager = User::factory()->create([
                'name' => 'Manager',
                'email' => 'manager@example.com',
            ]);
            $manager->assignRole('Manager');
        }

        if (!User::where('email', 'staff@example.com')->exists()) {
            $staff = User::factory()->create([
                'name' => 'Staff',
                'email' => 'staff@example.com',
            ]);
            $staff->assignRole('Staff');
        }
    }
}
