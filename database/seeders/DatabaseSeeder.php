<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\User::create([
            'login' => 'admin',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);
    }
}
