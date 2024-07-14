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
        Task::factory(100)
            ->withRandomDate()
            ->withOneOfGivenProjects(Project::all())
            ->withOneOfGivenOwner(User::all())
            ->create();
    }
}
