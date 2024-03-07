<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Note;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'description',
        'start_date',
        'due_date',
        'status',
        'priority',
    ];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    /**
     * Store a newly created task
     *
     * @param  array $taskData
     * 
     * @return App\Models\Task
     */
    public function storeTask($taskData)
    {
        return $this->create($taskData);
    }

    /**
     * Get task data
     *
     * @param  string $orderBy
     * @param  array $conditions
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAll($orderBy, $conditions)
    {
        $query = $this->with(['notes.attachments']);

        // loop all the search filters
        foreach ($conditions as $key => $searchValue) {
            // if note filter is applied then fetch tasks with at least 1 note
            if ($key == 'notes') {
                $query->has('notes', '>=', 1);
            } else {
                $query->where($key, '=', trim($searchValue));
            }
        }
        
        $query->orderByRaw('FIELD(priority, ' . $orderBy . ')');

        return $query->get();
    }
}
