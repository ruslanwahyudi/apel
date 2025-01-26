<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@banyupelle.com',
            'password' => bcrypt('a'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kepala User',
            'email' => 'kepala@banyupelle.com',
            'password' => bcrypt('a'),
            'role' => 'kepala',
        ]);

        User::create([
            'name' => 'Operator User',
            'email' => 'operator@banyupelle.com',
            'password' => bcrypt('a'),
            'role' => 'operator',
        ]);

        User::create([
            'name' => 'Regular User',
            'email' => 'user@banyupelle.com',
            'password' => bcrypt('a'),
            'role' => 'user',
        ]);
    }
}
