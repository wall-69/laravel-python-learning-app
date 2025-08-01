<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            "first_name" => "John",
            "last_name" => "Doe",
            "email" => "john@doe.com",
            "password" => Hash::make("doedoe")
        ]);

        Admin::create([
            "user_id" => $adminUser->id
        ]);
    }
}
