<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
        User::updateOrCreate(['email' => 'renzo.carianga@gmail.com'], ['name' => 'Admin','password'=>Hash::make('weneverknow')]);
        User::updateOrCreate(['email' => 'devops@joy-nostalg.com'], ['name' => 'Dev Ops','password'=>Hash::make('weneverknow')]);
    }
}
