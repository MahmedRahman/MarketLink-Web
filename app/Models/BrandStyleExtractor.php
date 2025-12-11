<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandStyleExtractor extends Model
{
    protected $fillable = [
        'organization_id',
        'project_id',
        'content_type',
        'content',
        'revenue',
        'currency',
        'brand_profile',
        'created_by'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'brand_profile' => 'array',
        'revenue' => 'decimal:2',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getContentTypeLabelAttribute()
    {
        $types = [
            'colors' => 'الألوان',
            'fonts' => 'الخطوط',
            'logos' => 'الشعارات',
            'images' => 'الصور',
            'text' => 'النصوص',
            'icons' => 'الأيقونات',
            'patterns' => 'الأنماط',
            'spacing' => 'المسافات',
            'post' => 'البوست',
            'reels' => 'الريلز',
            'book' => 'الكتاب',
            'other' => 'أخرى'
        ];

        return $types[$this->content_type] ?? $this->content_type;
    }
}
