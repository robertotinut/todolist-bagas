<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'created_by',
    ];

    /**
     * Get the users associated with the workspace.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'workspace_user')->withPivot('role')->withTimestamps();
    }

    /**
     * Get the user who created the workspace.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the areas inside this workspace.
     */
    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Get the tasks inside this workspace.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
