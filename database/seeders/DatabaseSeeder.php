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
        \App\Models\Employee::create([
            'firstName' => 'Admin',
            'lastName' => 'Owner',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'owner',
            'daily_salary' => 0,
            'isActive' => true,
        ]);

        \App\Models\Employee::create([
            'firstName' => 'Staff',
            'lastName' => 'Employee',
            'username' => 'employee',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'employee',
            'daily_salary' => 500,
            'isActive' => true,
        ]);
    }
}
