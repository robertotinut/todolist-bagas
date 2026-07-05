<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'workspace_id',
        'name',
        'icon',
        'color',
        'is_archived',
        'created_by',
        'is_default',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user who created the area.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the workspace that owns the area.
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the projects in this area.
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get the tasks in this area.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
