<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanTask extends Model
{
    protected $fillable = [
        'monthly_plan_id',
        'assigned_to',
        'title',
        'description',
        'status',
        'list_type',
        'order',
        'due_date',
        'color',
        'task_data',
    ];

    protected $casts = [
        'order' => 'integer',
        'due_date' => 'date',
        'task_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PlanTaskFile::class, 'plan_task_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'todo' => 'مهام',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'قيد المراجعة',
            'done' => 'مكتملة',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'todo' => 'gray',
            'in_progress' => 'blue',
            'review' => 'yellow',
            'done' => 'green',
            default => 'gray'
        };
    }
}
