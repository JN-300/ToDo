<?php

namespace Database\Factories;

use App\Enums\TaskStatusEnum;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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


    public function withRandomStatus(TaskStatusEnum ... $status):static
    {
        return $this->state(fn(array $attributes) => ['status' => fake()->randomElement($status)]);
    }
    public function withRandomDate(string $startDate = '-1 year', string $endDate = 'now'): static
    {
        return $this->state(function (array $attributes) use ($startDate,$endDate){
            $created_at = fake()->dateTimeBetween(startDate: $startDate, endDate: $endDate);
            $deadline = fake()->dateTimeBetween(startDate: $created_at, endDate: '+1 year');
            return [
                'created_at' => $created_at,
                'deadline' => $deadline
            ];
        });
    }

    public function withRandomDeadline(?string $startDate = null, ?string $endDate = null): static
    {
        return $this->state(function (array $attributes) use ($startDate,$endDate){
            $deadline = ($startDate && $endDate)
                ? fake()->dateTimeBetween(startDate: $startDate, endDate: $endDate)
                : (($endDate)
                    ? fake()->dateTime(max: $endDate)
                    : fake()->dateTimeBetween(startDate: $startDate, endDate:  '+1 year')
                )
            ;
            $created_at = fake()->dateTime($deadline);
//            $created_at = fake()->dateTimeBetween(startDate: $startDate, endDate: $deadline);
            return [
                'created_at' => $created_at,
                'deadline' => $deadline
            ];
        });
    }

    public function withProject(Project $project):static
    {
        return $this->state(fn(array $attributes) => ['project_id' => $project->id]);
    }
    public function withOneOfGivenProjects(Collection $projects): static
    {
        return $this->state(fn(array $attributes) => ['project_id' => fake()->randomElement($projects->pluck('id'))]);
    }

    public function withOwner(User $owner): static {
        return $this->state(fn(array $attributes) => ['owner_id' => $owner->id]);
    }
    public function withOneOfGivenOwner(Collection $owner): static
    {
        return $this->state(fn(array $attributes) => ['owner_id' => fake()->randomElement($owner->pluck('id'))]);
    }
}
