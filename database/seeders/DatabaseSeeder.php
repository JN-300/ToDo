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
            'name' => 'Admin',
            'email' => 'admin@example.de',
            'admin' => true
        ]);
        User::factory()->create([
            'name' => 'Max Mustermann',
            'email' => 'user@example.de',
            'admin' => true
        ]);

        User::factory(20)->create();
        $this->call([
            ProjectSeeder::class,
            TaskSeeder::class
        ]);
    }
}
