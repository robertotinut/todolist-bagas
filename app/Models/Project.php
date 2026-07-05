<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'area_id',
        'name',
        'description',
        'is_archived',
        'created_by',
        'is_default',
    ];

    protected $casts = [
        'is_archived' => 'boolean',
        'is_default' => 'boolean',
    ];

    /**
     * Get the user who created the project.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the area that owns the project.
     */
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    /**
     * Get the tasks in this project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
