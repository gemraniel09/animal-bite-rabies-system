<?php

namespace Database\Seeders;

use App\Models\Animal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $animals = ['Pet Dog', 'Cat', 'Rat'];

        foreach ($animals as $animal) {
            Animal::create([
                'name' => $animal
            ]);
        }
    }
}
