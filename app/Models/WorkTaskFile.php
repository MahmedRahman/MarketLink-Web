<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class WorkTaskFile extends Model
{
    protected $fillable = [
        'work_task_id',
        'file_name',
        'file_path',
        'file_type',
        'asset_kind',
        'file_size',
        'uploaded_by',
        'description',
        'nas_path',
        'nas_synced_at',
        'upload_batch',
        'nas_folder',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'nas_synced_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(WorkTask::class, 'work_task_id');
    }

    public function getFileUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getAssetKindLabelAttribute(): string
    {
        return match ($this->asset_kind) {
            'video' => 'فيديو',
            'pdf' => 'PDF',
            default => 'صورة',
        };
    }

    public function getFileIconAttribute(): string
    {
        return match ($this->asset_kind) {
            'video' => 'movie',
            'pdf' => 'picture_as_pdf',
            default => 'image',
        };
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) {
            return '—';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    public function isImage(): bool
    {
        return $this->asset_kind === 'image';
    }

    public function isVideo(): bool
    {
        return $this->asset_kind === 'video';
    }

    public function isPdf(): bool
    {
        return $this->asset_kind === 'pdf';
    }

    public function getNasPublicUrlAttribute(): ?string
    {
        if (! $this->nas_path) {
            return null;
        }

        return app(\App\Services\AcademyNasStorage::class)->publicUrl($this->nas_path);
    }

    public function getNasDisplayPathAttribute(): ?string
    {
        if (! $this->nas_path) {
            return null;
        }

        return '03_Social_Content/'.$this->nas_path;
    }

    public function getPublicShareUrlAttribute(): ?string
    {
        $task = $this->relationLoaded('task') ? $this->task : $this->task()->with('activity')->first();
        $token = $task?->activity?->share_token;
        if (! $token) {
            return null;
        }

        return route('public.work.file', [$token, $task->id, $this->id]);
    }
}
