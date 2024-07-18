<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixedUser = User::all()->where('email', 'user@example.de')->first();
        $otherUser = User::all()->whereNotIn('id', $fixedUser);

        // create 5 overdue tasks for fixed user
        Task::factory(5)
            ->withRandomDeadline(endDate: '-1 second')
            ->withOneOfGivenProjects(Project::all())
            ->withOwner($fixedUser)
            ->create();

        // create 10 active tasks for fixed user
        Task::factory(20)
            ->withRandomDeadline(startDate: '+1 week')
            ->withOneOfGivenProjects(Project::all())
            ->withOwner($fixedUser)
            ->create();

        // create 10 overdue tasks for other user
        Task::factory(20)
            ->withRandomDeadline(endDate: '-1 second')
            ->withOneOfGivenProjects(Project::all())
            ->withOneOfGivenOwner($otherUser)
            ->create();

        // create 100 active tasks for other user
        Task::factory(100)
            ->withRandomDeadline(startDate: '+1 week')
            ->withOneOfGivenProjects(Project::all())
            ->withOneOfGivenOwner($otherUser)
            ->create();
    }
}
