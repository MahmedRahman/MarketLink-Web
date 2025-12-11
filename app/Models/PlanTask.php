<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanTask extends Model
{
    protected $fillable = [
        'monthly_plan_id',
        'goal_id',
        'assigned_to',
        'title',
        'description',
        'idea',
        'post',
        'design',
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

    public function goal(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlanGoal::class, 'goal_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PlanTaskFile::class, 'plan_task_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PlanTaskComment::class, 'plan_task_id')->orderBy('created_at', 'desc');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'todo' => 'مهام',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'قيد المراجعة',
            'done' => 'مكتملة',
            'publish' => 'نشر',
            'archived' => 'أرشيف',
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
            'publish' => 'green',
            'archived' => 'slate',
            default => 'gray'
        };
    }

    public function getListTypeBadgeAttribute(): string
    {
        return match($this->list_type) {
            'tasks' => 'مهام',
            'employee' => 'موظف',
            'ready' => 'جاهز',
            'publish' => 'نشر',
            default => 'غير محدد'
        };
    }

    public function getListTypeColorAttribute(): string
    {
        return match($this->list_type) {
            'tasks' => 'gray',
            'employee' => 'blue',
            'ready' => 'yellow',
            'publish' => 'green',
            default => 'gray'
        };
    }
}
