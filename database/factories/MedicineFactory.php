<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

class MedicineFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()->id ?? 1,
            'name' => $this->faker->unique()->words(2, true),
            'type' => $this->faker->randomElement(['Tablet', 'Capsule', 'Syrup', 'Injection']),
            'stock' => $this->faker->numberBetween(10, 1000),
            'price' => $this->faker->randomFloat(2, 1, 500),
            'expiry_date' => $this->faker->dateTimeBetween('now', '+3 years')->format('Y-m-d'),
            'description' => $this->faker->sentence(10),
            'notes' => $this->faker->optional()->sentence(6),
            'image' => null,
            'status' => $this->faker->boolean(90), // 90% chance active
        ];
    }
}
