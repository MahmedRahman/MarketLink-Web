<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MonthlyPlan extends Model
{
    protected $fillable = [
        'project_id',
        'organization_id',
        'month',
        'year',
        'month_number',
        'description',
        'status',
    ];

    protected $casts = [
        'year' => 'integer',
        'month_number' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function goals(): HasMany
    {
        return $this->hasMany(MonthlyPlanGoal::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'monthly_plan_employees')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class)->orderBy('order');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'active' => 'نشطة',
            'completed' => 'مكتملة',
            'cancelled' => 'ملغاة',
            default => 'غير محدد'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'active' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }
}
