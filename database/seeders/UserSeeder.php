<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'admin@example.com';
        $name = 'Admin';
        $password = env('SEED_ADMIN_PASSWORD', 'troque-esta-senha');
        User::updateOrCreate(
            ['email' => $email],
            ['name' => $name, 'password' => $password]
        );
    }
}
