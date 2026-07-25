<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkActivity extends Model
{
    protected $fillable = [
        'organization_id',
        'created_by',
        'title',
        'type',
        'description',
        'event_date',
        'status',
    ];

    protected $casts = [
        'event_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(WorkTask::class)->orderBy('order')->orderBy('id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'live_lecture' => 'محاضرة لايف',
            'paid_round' => 'راوند مدفوع',
            'educational' => 'محتوى تعليمي',
            default => 'أخرى',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'live_lecture' => 'live_tv',
            'paid_round' => 'workspace_premium',
            'educational' => 'menu_book',
            default => 'category',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'تخطيط',
            'in_progress' => 'قيد التنفيذ',
            'done' => 'منجز',
            'cancelled' => 'ملغي',
            default => 'غير محدد',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'gray',
            'in_progress' => 'blue',
            'done' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getProgressAttribute(): int
    {
        $total = $this->tasks->count();
        if ($total === 0) {
            return 0;
        }
        $done = $this->tasks->where('status', 'done')->count();

        return (int) round(($done / $total) * 100);
    }

    public static function types(): array
    {
        return [
            'live_lecture' => 'محاضرة لايف',
            'paid_round' => 'راوند مدفوع',
            'educational' => 'محتوى تعليمي',
            'other' => 'أخرى',
        ];
    }

    public static function statuses(): array
    {
        return [
            'planning' => 'تخطيط',
            'in_progress' => 'قيد التنفيذ',
            'done' => 'منجز',
            'cancelled' => 'ملغي',
        ];
    }
}
