<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Email;
use App\Models\Suscriber;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Email>
 */
class EmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'suscriber_id' => Suscriber::factory(),
            'subject' => fake()->sentence(6),
            'content' => fake()->paragraphs(3),
            'status' => fake()->randomElement(['pending', 'sent', 'failed']),
            'tracking_token' => Str::uuid(),
            'sent_at' => now(),
        ];
    }
}
