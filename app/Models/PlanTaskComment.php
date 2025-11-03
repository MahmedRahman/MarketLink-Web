<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanTaskComment extends Model
{
    protected $fillable = [
        'plan_task_id',
        'user_id',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the task that owns the comment.
     */
    public function planTask(): BelongsTo
    {
        return $this->belongsTo(PlanTask::class, 'plan_task_id');
    }

    /**
     * Get the user who made the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
