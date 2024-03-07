<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Task;
use App\Models\Note;
use App\Models\NoteAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;


class TaskController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->task = new Task;
    }

    /**
     * Store a newly created task with notes and attachments.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // Validate the incoming request
        $this->request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date_format:d-m-Y',
            'due_date' => 'required|date_format:d-m-Y|after_or_equal:start_date',
            'status' => 'required|in:New,Incomplete,Complete',
            'priority' => 'required|in:High,Medium,Low',
            'notes' => 'sometimes|array',
            'notes.*.subject' => 'required_with:notes|string|max:255',
            'notes.*.note' => 'required_with:notes|string',
            'notes.*.attachments' => 'sometimes|array',
            'notes.*.attachments.*' => 'file',
        ]);

        DB::beginTransaction();
        try {

            // Convert dates from d/m/Y to Y-m-d format for database storage
            $startDate = date('Y-m-d', strtotime($this->request->input('start_date')));
            $dueDate = date('Y-m-d', strtotime($this->request->input('due_date')));

            $taskData = $this->request->only(['subject', 'description', 'status', 'priority']);
            $taskData['start_date'] = $startDate;
            $taskData['due_date'] = $dueDate;

            // Create the task
            $task = $this->task->storeTask($taskData);

            // Iterate over each note
            foreach ($this->request->notes ?? [] as $noteData) {
                // Store Notes
                $note = $task->notes()->create([
                    'subject' => $noteData['subject'],
                    'note' => $noteData['note'],
                ]);
                
                // Check if there are attachments for the note
                if (isset($noteData['attachments'])) {
                    foreach ($noteData['attachments'] as $attachment) {
                        // Store the attachment and create a NoteAttachment entry
                        $filePath = $attachment->store('note_attachments');
                        $mimeType = Storage::mimeType($filePath);

                        // Store attachments
                        $attachment = $note->attachments()->create(['file_path' => $filePath, 'mime_type' => $mimeType]);
                    }
                }
            }
            DB::commit();

            return response()->json(['message' => 'Task with notes and attachments created successfully', 'task' => $task], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created task with notes and attachments.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderBy = '\'' . implode('\', \'', config('constants.TASK_PRIORITIES')) . '\'';

        $conditions = [];
        $notes = false;

        if ($this->request->query('filter')) {
            $filters = $this->request->query('filter');

            if (is_array($filters) && !empty($filters)) {
                foreach ($filters as $key => $value) {
                    $conditions[$key] = $value;
                }
            }
        }

        // Fetch all the records
        $tasks = $this->task->getAll($orderBy, $conditions);
        
        $response = ['data' => $tasks, 'message' => 'List fetched successfully', 'status' => Response::HTTP_OK];
        
        if ($tasks->isEmpty()) {
            $response = ['data' => [], 'message' => 'Data not found', 'status' => Response::HTTP_OK];
        } 
        
        return response($response);
    }
}
