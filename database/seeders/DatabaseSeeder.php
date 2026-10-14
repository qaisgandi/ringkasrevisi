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
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@bps.go.id'],
            [
                'name' => 'Admin BPS',
                'username' => 'admin1',
                'password' => \Illuminate\Support\Facades\Hash::make('admin1234'),
                'role' => 'admin',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'user@bps.go.id'],
            [
                'name' => 'User Publik',
                'username' => 'user1',
                'password' => \Illuminate\Support\Facades\Hash::make('user1234'),
                'role' => 'user',
            ]
        );
    }
}
