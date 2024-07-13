<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Jeffrey Nellissen',
            'email' => 'jeffrey@nellissen.net',
        ]);

        $this->call([
            ProjectSeeder::class,
            TaskSeeder::class
        ]);
    }
}
