<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'workspace_id',
        'user_id',
        'report_date',
        'productivity_rating',
        'tasks_completed',
        'notes',
        'obstacles',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    /**
     * Get the workspace of the daily report.
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * Get the user who created the daily report.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
