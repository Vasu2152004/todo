<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Todo;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    public function run(): void
    {
        $todos = [
            [
                'title' => 'Complete Laravel Todo App',
                'description' => 'Build a production-ready todo list application with Bootstrap UI',
                'priority' => 'high',
                'completed' => false,
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Write Documentation',
                'description' => 'Create comprehensive README with setup instructions',
                'priority' => 'medium',
                'completed' => false,
                'due_date' => now()->addDays(2),
            ],
            [
                'title' => 'Test Application',
                'description' => 'Test all CRUD operations and edge cases',
                'priority' => 'high',
                'completed' => false,
                'due_date' => now()->addDays(1),
            ],
            [
                'title' => 'Deploy to Production',
                'description' => 'Configure production environment and deploy',
                'priority' => 'medium',
                'completed' => false,
                'due_date' => now()->addDays(5),
            ],
            [
                'title' => 'Review Code',
                'description' => 'Code review and optimization',
                'priority' => 'low',
                'completed' => true,
                'due_date' => now()->subDays(1),
            ],
        ];

        foreach ($todos as $todo) {
            Todo::create($todo);
        }
    }
}
