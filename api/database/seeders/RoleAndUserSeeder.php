<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@reservation.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'timezone' => 'Asia/Singapore',
            ]
        );
        $admin->assignRole($adminRole);

        $user = User::firstOrCreate(
            ['email' => 'user@reservation.com'],
            [
                'name' => 'User Reservator',
                'password' => Hash::make('user123'),
                'timezone' => 'Asia/Singapore',
            ]
        );
        $user->assignRole($userRole);
    }
}
