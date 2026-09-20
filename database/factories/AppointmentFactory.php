<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
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
            'duration_minutes' => fake()->numberBetween(15, 480),
            'project_id' => Project::factory(),
            'project_task' => fake()->sentence(3),
            'occurrence' => fake()->sentence(5),
            'internal_description' => fake()->paragraph(),
            'entry_type' => fake()->randomElement(['work', 'overtime']),
            'owner' => fake()->name(),
        ];
    }
}
