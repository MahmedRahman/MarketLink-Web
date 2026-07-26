<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkTask extends Model
{
    protected $fillable = [
        'work_activity_id',
        'assigned_to',
        'content_writer_id',
        'designer_id',
        'title',
        'idea',
        'tov',
        'caption',
        'content_type',
        'design_reference',
        'designer_brief',
        'platforms',
        'notes',
        'kind',
        'status',
        'due_date',
        'publish_date',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'publish_date' => 'date',
        'platforms' => 'array',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(WorkActivity::class, 'work_activity_id');
    }

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function contentWriter(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'content_writer_id');
    }

    public function designer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'designer_id');
    }

    /**
     * مهام يظهر فيها الموظف كمعيّن أو كاتب محتوى أو مصمم.
     */
    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where(function (Builder $q) use ($employeeId) {
            $q->where('assigned_to', $employeeId)
                ->orWhere('content_writer_id', $employeeId)
                ->orWhere('designer_id', $employeeId);
        });
    }

    public function isVisibleToEmployee(int $employeeId): bool
    {
        return (int) $this->assigned_to === $employeeId
            || (int) $this->content_writer_id === $employeeId
            || (int) $this->designer_id === $employeeId;
    }

    public function getKindLabelAttribute(): string
    {
        return match ($this->kind) {
            'design' => 'تصميم',
            'video' => 'فيديو',
            'content' => 'محتوى',
            'publish' => 'نشر',
            default => 'أخرى',
        };
    }

    public function getKindColorAttribute(): string
    {
        return match ($this->kind) {
            'design' => 'purple',
            'video' => 'red',
            'content' => 'blue',
            'publish' => 'teal',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'todo' => 'لم تبدأ',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'قيد المراجعة',
            'done' => 'منجزة',
            default => 'غير محدد',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'todo' => 'gray',
            'in_progress' => 'blue',
            'review' => 'yellow',
            'done' => 'green',
            default => 'gray',
        };
    }

    public function getContentTypeLabelAttribute(): ?string
    {
        if (! $this->content_type) {
            return null;
        }

        return self::contentTypes()[$this->content_type] ?? $this->content_type;
    }

    public function getPlatformLabelsAttribute(): array
    {
        $map = self::platforms();
        $platforms = $this->platforms ?? [];

        return collect($platforms)
            ->map(fn ($key) => $map[$key] ?? $key)
            ->values()
            ->all();
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date
            && $this->status !== 'done'
            && $this->due_date->isPast()
            && ! $this->due_date->isToday();
    }

    /**
     * يقترح موظفًا نشطًا من المنظمة حسب نوع الشغل ودوره.
     */
    public static function suggestAssigneeId(?int $organizationId, string $kind): ?int
    {
        $role = self::kindRoleMap()[$kind] ?? null;
        if (! $role || ! $organizationId) {
            return null;
        }

        return Employee::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->where('role', $role)
            ->orderBy('id')
            ->value('id');
    }

    /**
     * الدور المقترح لكل نوع شغل (kind => employee role).
     */
    public static function kindRoleMap(): array
    {
        return [
            'design' => 'designer',
            'video' => 'video_editor',
            'content' => 'content_writer',
            'publish' => 'account_manager',
        ];
    }

    public static function kinds(): array
    {
        return [
            'design' => 'تصميم',
            'video' => 'فيديو',
            'content' => 'محتوى',
            'publish' => 'نشر',
            'other' => 'أخرى',
        ];
    }

    public static function statuses(): array
    {
        return [
            'todo' => 'لم تبدأ',
            'in_progress' => 'قيد التنفيذ',
            'review' => 'قيد المراجعة',
            'done' => 'منجزة',
        ];
    }

    public static function contentTypes(): array
    {
        return [
            'post' => 'بوست',
            'reels' => 'ريلز',
            'carousel' => 'كروسيل',
        ];
    }

    public static function platforms(): array
    {
        return [
            'facebook' => 'فيسبوك',
            'instagram' => 'إنستجرام',
            'linkedin' => 'لينكدإن',
            'tiktok' => 'تيك توك',
            'twitter' => 'إكس (تويتر)',
        ];
    }
}
