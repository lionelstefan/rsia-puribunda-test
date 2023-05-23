<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

		DB::table('unit')->insert([
            'name' => 'Medis',
        ]);

		DB::table('unit')->insert([
            'name' => 'Rawat Inap',
        ]);

		DB::table('jabatan')->insert([
            'name' => 'Staff',
        ]);

		DB::table('jabatan')->insert([
            'name' => 'Direktur',
        ]);

		User::factory()
			->count(10)
			->create();
    }
}
