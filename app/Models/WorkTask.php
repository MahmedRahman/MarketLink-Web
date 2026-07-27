<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'publish_links',
        'notes',
        'kind',
        'status',
        'pipeline_stage',
        'due_date',
        'publish_date',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
        'publish_date' => 'date',
        'platforms' => 'array',
        'publish_links' => 'array',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(WorkActivity::class, 'work_activity_id');
    }

    public function getPublicShareUrlAttribute(): ?string
    {
        $activity = $this->relationLoaded('activity') ? $this->activity : $this->activity()->first();
        if (! $activity?->share_token) {
            return null;
        }

        return route('public.work.task', [$activity->share_token, $this->id]);
    }

    public function ensurePublicShareUrl(): ?string
    {
        $activity = $this->activity;
        if (! $activity) {
            return null;
        }

        $activity->ensureShareToken();

        return route('public.work.task', [$activity->share_token, $this->id]);
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

    public function files(): HasMany
    {
        return $this->hasMany(WorkTaskFile::class)->latest();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(WorkTaskLog::class)->latest();
    }

    /**
     * يسجّل حدثًا في سجل التاسك.
     */
    public function logEvent(
        string $action,
        string $message,
        ?string $field = null,
        mixed $from = null,
        mixed $to = null,
        ?array $meta = null,
        ?int $userId = null
    ): WorkTaskLog {
        return $this->logs()->create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'field' => $field,
            'from_value' => $from !== null ? (string) $from : null,
            'to_value' => $to !== null ? (string) $to : null,
            'message' => $message,
            'meta' => $meta,
        ]);
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

    /**
     * مهام الموظف حسب مسؤول المرحلة الحالية فقط
     * (كتابة → كاتب | تصميم → مصمم | نشر → المسؤول).
     */
    public function scopeForEmployeeCurrentStage(Builder $query, int $employeeId): Builder
    {
        return $query->where(function (Builder $q) use ($employeeId) {
            $q->where(function (Builder $writing) use ($employeeId) {
                $writing->where('pipeline_stage', 'writing')
                    ->where(function (Builder $owner) use ($employeeId) {
                        $owner->where('content_writer_id', $employeeId)
                            ->orWhere(function (Builder $fallback) use ($employeeId) {
                                $fallback->whereNull('content_writer_id')
                                    ->where('assigned_to', $employeeId);
                            });
                    });
            })->orWhere(function (Builder $design) use ($employeeId) {
                $design->where('pipeline_stage', 'design')
                    ->where(function (Builder $owner) use ($employeeId) {
                        $owner->where('designer_id', $employeeId)
                            ->orWhere(function (Builder $fallback) use ($employeeId) {
                                $fallback->whereNull('designer_id')
                                    ->where('assigned_to', $employeeId);
                            });
                    });
            })->orWhere(function (Builder $publish) use ($employeeId) {
                $publish->whereIn('pipeline_stage', ['ready_to_publish', 'published'])
                    ->where('assigned_to', $employeeId);
            });
        });
    }

    public function stageOwnerId(?string $stage = null): ?int
    {
        $stage ??= $this->pipeline_stage;

        return match ($stage) {
            'design' => $this->designer_id ?? $this->assigned_to,
            'ready_to_publish', 'published' => $this->assigned_to,
            default => $this->content_writer_id ?? $this->assigned_to,
        };
    }

    public function isVisibleToEmployee(int $employeeId): bool
    {
        return (int) $this->stageOwnerId() === (int) $employeeId;
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
            'done' => 'اكتمال',
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

    public function getPipelineStageLabelAttribute(): string
    {
        return self::pipelineStages()[$this->pipeline_stage] ?? 'كتابة المحتوى';
    }

    public function getPipelineStageColorAttribute(): string
    {
        return match ($this->pipeline_stage) {
            'design' => 'purple',
            'ready_to_publish' => 'teal',
            'published' => 'green',
            default => 'blue',
        };
    }

    public function getPipelineStageIconAttribute(): string
    {
        return match ($this->pipeline_stage) {
            'design' => 'palette',
            'ready_to_publish' => 'schedule_send',
            'published' => 'check_circle',
            default => 'edit_note',
        };
    }

    public function getOwnerForCurrentStageAttribute(): ?Employee
    {
        return match ($this->pipeline_stage) {
            'design' => $this->designer ?? $this->assignedEmployee,
            'ready_to_publish', 'published' => $this->assignedEmployee,
            default => $this->contentWriter ?? $this->assignedEmployee,
        };
    }

    public static function suggestedDesignAssetKind(?string $contentType): string
    {
        return match ($contentType) {
            'reels' => 'video',
            default => 'image',
        };
    }

    public static function designAssetKinds(): array
    {
        return [
            'image' => 'صورة',
            'video' => 'فيديو',
            'pdf' => 'PDF',
        ];
    }

    public static function pipelineStages(): array
    {
        return [
            'writing' => 'كتابة المحتوى',
            'design' => 'التصميم',
            'ready_to_publish' => 'جاهز للنشر',
            'published' => 'تم النشر',
        ];
    }

    public static function nextPipelineStage(?string $stage): ?string
    {
        return match ($stage) {
            'writing' => 'design',
            'design' => 'ready_to_publish',
            'ready_to_publish' => 'published',
            default => null,
        };
    }

    public static function previousPipelineStage(?string $stage): ?string
    {
        return match ($stage) {
            'design' => 'writing',
            'ready_to_publish' => 'design',
            'published' => 'ready_to_publish',
            default => null,
        };
    }

    public function publishLinkFor(string $platform): ?string
    {
        $links = $this->publish_links ?? [];

        return is_array($links) ? ($links[$platform] ?? null) : null;
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
            'done' => 'اكتمال',
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
