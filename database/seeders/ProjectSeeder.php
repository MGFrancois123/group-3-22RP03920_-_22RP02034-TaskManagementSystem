<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\ManagerScore;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run()
    {
        // Get or create a manager
        $manager = User::where('role', 'manager')->first();
        if (!$manager) {
            $manager = User::factory()->create([
                'name' => 'Test Manager',
                'email' => 'manager@example.com',
                'role' => 'manager',
                'password' => bcrypt('password'),
            ]);
        }

        // Create some projects
        $projects = [
            [
                'name' => 'Website Redesign',
                'description' => 'Redesign the company website with modern UI/UX',
            ],
            [
                'name' => 'Mobile App Development',
                'description' => 'Develop a new mobile application for iOS and Android',
            ],
            [
                'name' => 'Database Migration',
                'description' => 'Migrate the existing database to a new structure',
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create([
                'name' => $projectData['name'],
                'description' => $projectData['description'],
                'created_by' => $manager->id,
            ]);

            // Assign the manager to the project
            $project->users()->attach($manager->id);

            // Create some tasks for each project
            $tasks = [
                [
                    'name' => 'Design Mockups',
                    'description' => 'Create UI/UX mockups for the project',
                    'status' => 'Completed',
                    'due_date' => now()->subDays(2),
                    'quality_score' => 85,
                    'timeliness_score' => 90,
                    'feedback' => 'Good progress on the mockups. Please ensure to include mobile responsiveness in the next iteration.',
                    'evaluation_notes' => 'User showed good understanding of UI/UX principles. Need to focus more on accessibility features.',
                    'evaluation_type' => 'Initial Review',
                    'evaluation_criteria' => ['UI Design', 'User Experience', 'Responsiveness'],
                    'needs_follow_up' => true,
                    'follow_up_date' => now()->addDays(3),
                ],
                [
                    'name' => 'Frontend Development',
                    'description' => 'Implement the frontend components',
                    'status' => 'In-Progress',
                    'due_date' => now()->addDays(14),
                    'quality_score' => 75,
                    'timeliness_score' => 80,
                    'feedback' => 'Good progress on the frontend components. Need to improve code organization.',
                    'evaluation_notes' => 'User is making steady progress. Code quality needs improvement.',
                    'evaluation_type' => 'Progress Review',
                    'evaluation_criteria' => ['Code Quality', 'Functionality', 'Performance'],
                    'needs_follow_up' => true,
                    'follow_up_date' => now()->addDays(5),
                ],
                [
                    'name' => 'Backend Integration',
                    'description' => 'Integrate frontend with backend services',
                    'status' => 'Pending',
                    'due_date' => now()->addDays(21),
                    'quality_score' => null,
                    'timeliness_score' => null,
                    'feedback' => null,
                    'evaluation_notes' => null,
                    'evaluation_type' => null,
                    'evaluation_criteria' => null,
                    'needs_follow_up' => false,
                    'follow_up_date' => null,
                ],
            ];

            foreach ($tasks as $taskData) {
                $task = Task::create([
                    'project_id' => $project->id,
                    'name' => $taskData['name'],
                    'description' => $taskData['description'],
                    'status' => $taskData['status'],
                    'due_date' => $taskData['due_date'],
                    'assigned_to' => $manager->id,
                    'created_by' => $manager->id,
                ]);

                // If the task has evaluation data, create a ManagerScore record
                if ($taskData['quality_score'] !== null) {
                    ManagerScore::create([
                        'manager_id' => $manager->id,
                        'task_id' => $task->id,
                        'quality_score' => $taskData['quality_score'],
                        'timeliness_score' => $taskData['timeliness_score'],
                        'feedback' => $taskData['feedback'],
                        'evaluation_notes' => $taskData['evaluation_notes'],
                        'evaluation_type' => $taskData['evaluation_type'],
                        'evaluation_criteria' => $taskData['evaluation_criteria'],
                        'needs_follow_up' => $taskData['needs_follow_up'],
                        'follow_up_date' => $taskData['follow_up_date'],
                        'evaluated_at' => now(),
                    ]);
                }
            }
        }
    }
}
