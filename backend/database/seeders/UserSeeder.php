<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'johndoe',
            'first_name' => 'John',
            'middle_name' => 'D',
            'last_name' => 'Doe',
            'email' => 'johndoe@mail.com',
            'password' => 'password',
            'role' => 0,
        ]);
    }
}
