<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::first();

        // Create first project with ENV config
        $testProjectUrl = env('TEST_PROJECT_URL');
        if ($testProjectUrl) {
            $user->projects()->updateOrCreate([
                'url' => $testProjectUrl
            ]);
        }

        $project = Project::factory()
            ->count(1)
            ->for($user)
            ->create();
    }
}
