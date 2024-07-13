<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(50),
            'description' => fake()->text(200),
            'status' => fake()->randomElement(TaskStatusEnum::cases()),
            'deadline' => fake()->dateTimeBetween(startDate: '+1 day', endDate: '+1 year')
        ];
    }

    public function withRandomDate(): static
    {
        return $this->state(function (array $attributes) {
            $created_at = fake()->dateTimeBetween(startDate: '-1 year');
            $deadline = fake()->dateTimeBetween(startDate: $created_at, endDate: '+1 year');
            return [
                'created_at' => $created_at,
                'deadline' => $deadline
            ];
        }
        );
    }
}
