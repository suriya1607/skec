<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@srikumaran.in'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );

        $this->command->info('Admin user created: admin@srikumaran.in / Admin@123');
    }
}
