<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'cashier',
            'password' => Hash::make('123'),
            'role' => 'cashier',
        ]);
    }
}
