<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Task;

class TaskNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the number of tasks, notes per task, and the attachment ratio
        $numTasks = 10;
        $numNotesPerTask = 3;

        // Define dummy priorities and statuses
        $priorities = config('constants.TASK_PRIORITIES');
        $statuses = config('constants.TASK_STATUSES');

        // Loop to create tasks
        DB::beginTransaction();
        for ($i = 0; $i < $numTasks; $i++) {
            $task = Task::create([
                'subject' => 'Task ' . $i,
                'description' => 'Description for task ' . $i,
                'start_date' => Carbon::today()->subDays(rand(1, 10)),
                'due_date' => Carbon::today()->addDays(rand(1, 10)),
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Randomly decide if the task should have notes (50% chance)
            if (rand(0, 1)) {
                // Loop to create notes for each task
                for ($j = 0; $j < $numNotesPerTask; $j++) {
                    $note = $task->notes()->create([
                        'subject' => 'Note ' . $j . ' for Task ' . $i,
                        'note' => 'This is a note for task ' . $i . '. Note number: ' . $j,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
    
                    // Decide randomly if the note should have an attachment
                    if (rand(0, 1) ) {
                        $note->attachments()->create([
                            'file_path' => 'path/to/dummy/file' . $j . '.txt',
                            'mime_type' => 'text/plain',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
        DB::commit();
    }
}
