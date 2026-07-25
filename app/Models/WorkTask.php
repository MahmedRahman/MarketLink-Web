<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkTask extends Model
{
    protected $fillable = [
        'work_activity_id',
        'assigned_to',
        'title',
        'idea',
        'notes',
        'kind',
        'status',
        'due_date',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
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
}
