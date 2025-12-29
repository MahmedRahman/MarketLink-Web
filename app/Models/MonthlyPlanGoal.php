<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyPlanGoal extends Model
{
    protected $fillable = [
        'monthly_plan_id',
        'goal_type',
        'goal_name',
        'target_value',
        'achieved_value',
        'posts',
        'carousel',
        'reels',
        'video',
        'photo',
        'ads_campaigns',
        'other_goals',
        'description',
    ];

    protected $casts = [
        'target_value' => 'integer',
        'achieved_value' => 'integer',
        'posts' => 'integer',
        'carousel' => 'integer',
        'reels' => 'integer',
        'video' => 'integer',
        'photo' => 'integer',
        'ads_campaigns' => 'integer',
        'other_goals' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function monthlyPlan(): BelongsTo
    {
        return $this->belongsTo(MonthlyPlan::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(PlanTask::class, 'goal_id');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->target_value == 0) {
            return 0;
        }
        return min(100, ($this->achieved_value / $this->target_value) * 100);
    }
}
