<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'manager',
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'manager'
        ]);

        User::create([
            'username' => 'agent',
            'password' => Hash::make('PASSWORD'),
            'is_active' => true,
            'role' => 'agent'
        ]);
    }
}
