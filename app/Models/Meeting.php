<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    protected $fillable = [
        'organization_id',
        'responsible_employee_id',
        'project_id',
        'meeting_date',
        'meeting_time',
        'title',
        'objective',
        'meeting_link',
        'attendees'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'attendees' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function responsibleEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'responsible_employee_id');
    }
}
