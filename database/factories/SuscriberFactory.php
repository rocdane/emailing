<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Suscriber>
 */
class SuscriberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {        
        return [
            'email' => fake()->unique()->safeEmail(),
            'lang' => fake()->randomElement(['fr', 'en', 'es', 'de']),
            'name' => fake()->name(),
            'is_active' => fake()->boolean(80),
        ];
    }
}
