<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء المستخدم الإداري
        User::updateOrCreate(
            ['email' => 'admin@marketlink.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@marketlink.com',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin user created successfully!');
    }
}

