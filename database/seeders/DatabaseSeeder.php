<?php

namespace Database\Seeders;
use DB;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        DB::table('users')->insert([
            "email"=>'admin@gomezpalacio.gob.mx',
            'password' => Hash::make('admin123'),
              'role' => 0,
           ]);
    }
}
