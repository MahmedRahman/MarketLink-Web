<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkTaskLog extends Model
{
    protected $fillable = [
        'work_task_id',
        'user_id',
        'action',
        'field',
        'from_value',
        'to_value',
        'message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(WorkTask::class, 'work_task_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'stage_changed' => 'swap_horiz',
            'status_changed' => 'flag',
            'assignee_changed' => 'person',
            'created' => 'add_circle',
            'file_uploaded' => 'upload_file',
            'publish_links_updated' => 'link',
            default => 'history',
        };
    }

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'stage_changed' => 'indigo',
            'status_changed' => 'amber',
            'assignee_changed' => 'purple',
            'created' => 'teal',
            'file_uploaded' => 'blue',
            'publish_links_updated' => 'green',
            default => 'gray',
        };
    }
}
