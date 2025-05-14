<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bodyParts = [
            'Head', 'Neck', 'Shoulder', 'Arm', 'Elbow', 'Hand', 'Fingers',
            'Chest', 'Abdomen', 'Back', 'Hip', 'Leg', 'Knee', 'Foot', 'Toes'
        ];

        $animalStatuses = ['Alive', 'Lost', 'Dead', 'Re-Exposed'];
        $timestamp = $this->faker->dateTimeBetween('-6 months', 'now');
        return [
            'place' => 'Outside',
            'patient_id' => $this->faker->randomElement(range(1, 50)),
            'animal_id' => $this->faker->randomElement([1, 2, 3]),
            'barangay_id' => $this->faker->randomElement(range(1, 22)),
            'animal_status' => $this->faker->randomElement($animalStatuses),
            'brand_id' => 1,
            'body_part' => $this->faker->randomElement($bodyParts),
            'category' => $this->faker->randomElement(range(1, 3)),
            'vaccination_status' => 'required',
            'wash_bite' => $this->faker->randomElement([0, 1]),
            'rig_given' => $this->faker->randomElement([0, 1]),
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
            'booster_given' => $this->faker->randomElement([0, 1]),
        ];
    }
}
