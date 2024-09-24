<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([[
            'name' => 'Admin',
            'email' => 'mdbellalhossain798@gmail.com',
            'user_type' => 'admin', // Replace with your desired user type
            'password' => bcrypt('12345678'), // Replace with your desired password
        ],
        [
            'name' => 'Rakib',
            'email' => 'rakib@gmail.com',
            'user_type' => 'customer', // Replace with your desired user type
            'password' => bcrypt('12345678'), // Replace with your desired password
        ],
        [
            'name' => 'Anik',
            'email' => 'anik@gmail.com',
            'user_type' => 'customer', // Replace with your desired user type
            'password' => bcrypt('12345678'), // Replace with your desired password
        ],
        [
            'name' => 'Sakib',
            'email' => 'sakib@gmail.com',
            'user_type' => 'customer', // Replace with your desired user type
            'password' => bcrypt('12345678'), // Replace with your desired password
        ]]);
    }
}
