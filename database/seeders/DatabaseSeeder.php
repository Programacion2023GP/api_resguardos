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
            "name"=>'Super Admin sistemas',
            "payroll"=>'999999',
            'group'=>'CETIC',
            'role' => 1,
            'password' => Hash::make('Superadmin2024')
           
        ]);
        $this->call([
            GuardsSeeder::class,
          
        ]);
    }
}
