<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'workspace_id',
        'area_id',
        'project_id',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'reminder_at',
        'estimate_time',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'reminder_at' => 'datetime',
    ];

    /**
     * Get the workspace that owns the task.
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the area that contains the task.
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get the project that contains the task.
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the subtasks for the task.
     */
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    /**
     * Get the user who created the task.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attachments for the task.
     */
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}
