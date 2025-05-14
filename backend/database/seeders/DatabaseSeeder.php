<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            BarangaySeeder::class,
            UserSeeder::class,
//            PatientSeeder::class,
//            AnimalSeeder::class,
//            BrandSeeder::class,
//            TransactionSeeder::class
        ]);
    }
}
