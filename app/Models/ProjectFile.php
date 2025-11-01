<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectFile extends Model
{
    protected $fillable = [
        'project_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the project that owns the file.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the file URL.
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get formatted file size.
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'غير معروف';
        }

        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get file icon based on type.
     */
    public function getFileIconAttribute(): string
    {
        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION);
        
        return match(strtolower($extension)) {
            'pdf' => 'picture_as_pdf',
            'doc', 'docx' => 'description',
            'xls', 'xlsx' => 'table_chart',
            'ppt', 'pptx' => 'slideshow',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
            'zip', 'rar', '7z' => 'folder_zip',
            'txt' => 'text_snippet',
            default => 'attach_file'
        };
    }
}
