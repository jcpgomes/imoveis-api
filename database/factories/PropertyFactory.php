<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\PropertyStatus;
use App\Models\PropertyType;
/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_code' => fake()->unique()->numerify('IMV-#####'),
            'title' => fake()->sentence(4),
            'price' => fake()->randomFloat(2, 80000, 5000000),
            'property_type_id' => PropertyType::query()->inRandomOrder()->value('id'),
            'city' => fake()->randomElement(['Rio de Janeiro', 'Niterói', 'Teresópolis', 'Petrópolis', 'Magé']),
            'state' => 'RJ',
            'status' => fake()->randomElement(PropertyStatus::cases())
        ];
    }
}
