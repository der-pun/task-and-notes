<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NoteAttachment;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'subject',
        'note',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function attachments()
    {
        return $this->hasMany(NoteAttachment::class);
    }
}
