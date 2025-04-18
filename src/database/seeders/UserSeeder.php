<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'id' => 1,
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'status_id' => 1,
            'is_admin' => false,
        ]);

        User::create([
            'id' => 2,
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin1234'),
            'status_id' => 1,
            'is_admin' => true,
        ]);
    }
}
