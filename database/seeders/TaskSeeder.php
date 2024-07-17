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
        // create 10 overdue
        Task::factory(10)
            ->withRandomDeadline(endDate: '-1 second')
            ->withOneOfGivenProjects(Project::all())
            ->withOneOfGivenOwner(User::all())
            ->create();

        // create 10 overdue
        Task::factory(20)
            ->withRandomDeadline(startDate: '+1 week')
            ->withOneOfGivenProjects(Project::all())
            ->withOneOfGivenOwner(User::all())
            ->create();
    }
}
