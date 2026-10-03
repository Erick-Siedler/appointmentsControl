<?php

namespace Database\Factories;

use App\Models\DailyNote;
use App\Models\PendingTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PendingTask>
 */
class PendingTaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_note_id' => DailyNote::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status' => 'not_started',
            'priority' => 'normal',
            'due_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'assignee' => fake()->name(),
        ];
    }
}
