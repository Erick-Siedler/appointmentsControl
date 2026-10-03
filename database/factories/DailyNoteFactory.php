<?php

namespace Database\Factories;

use App\Models\DailyNote;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DailyNote>
 */
class DailyNoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'body' => fake()->paragraph(),
            'author_token' => (string) Str::uuid(),
        ];
    }
}
