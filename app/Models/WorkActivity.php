<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        'share_token',
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

    public function ensureShareToken(): string
    {
        if (! $this->share_token) {
            $this->forceFill([
                'share_token' => Str::random(40),
            ])->save();
        }

        return $this->share_token;
    }

    public function regenerateShareToken(): string
    {
        $this->forceFill([
            'share_token' => Str::random(40),
        ])->save();

        return $this->share_token;
    }

    public function disableShareToken(): void
    {
        $this->forceFill(['share_token' => null])->save();
    }

    public function getPublicShareUrlAttribute(): ?string
    {
        if (! $this->share_token) {
            return null;
        }

        return route('public.work.show', $this->share_token);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'free_lecture' => 'محاضرة مجانية',
            'live_lecture' => 'محاضرة لايف',
            'paid_round' => 'راوند مدفوع',
            'educational' => 'محتوى تعليمي',
            default => 'أخرى',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'free_lecture' => 'smart_display',
            'live_lecture' => 'live_tv',
            'paid_round' => 'workspace_premium',
            'educational' => 'menu_book',
            default => 'category',
        };
    }

    /**
     * هل النشاط محاضرة (مجانية/لايف) تتبع دليل تنظيم ملفات المحاضرة؟
     */
    public function getIsLectureAttribute(): bool
    {
        return in_array($this->type, ['free_lecture', 'live_lecture'], true);
    }

    /**
     * المسار المقترح لفولدر المحاضرة حسب دليل التنظيم:
     * 04_Rounds/Free_Lectures/[التاريخ]_[الموضوع]_[المحاضر]/
     */
    public function getSuggestedFolderAttribute(): string
    {
        $date = $this->event_date ? $this->event_date->format('Y-m-d') : 'YYYY-MM-DD';

        return "04_Rounds/Free_Lectures/{$date}_[الموضوع]_[المحاضر]/";
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
            'free_lecture' => 'محاضرة مجانية',
            'live_lecture' => 'محاضرة لايف',
            'paid_round' => 'راوند مدفوع',
            'educational' => 'محتوى تعليمي',
            'other' => 'أخرى',
        ];
    }

    /**
     * قالب التاسكات القياسية للمحاضرة المجانية — مأخوذ من
     * «دليل تنظيم ملفات المحاضرة» (Maha Elkhadry Academy).
     *
     * offset = عدد الأيام بالنسبة لتاريخ المحاضرة (سالب = قبلها).
     */
    public static function lectureTaskTemplate(): array
    {
        return [
            [
                'title' => 'تصميم بوست إعلان المحاضرة',
                'kind' => 'content',
                'offset' => -3,
                'idea' => "ينشر قبل المحاضرة بأيام لإعلان الموعد والموضوع.\nالمسار: Marketing_Graphics/Announcement/Announcement_Post.png",
                'content_type' => 'post',
                'platforms' => ['facebook', 'instagram'],
            ],
            [
                'title' => 'فيديو تشويقي قبل المحاضرة (Teaser_Before)',
                'kind' => 'video',
                'offset' => -2,
                'idea' => "فيديو قصير يشجع الناس تحضر اللايف.\nالمسار: Marketing_Clips/Teasers/Teaser_Before.mp4",
            ],
            [
                'title' => 'تصميم بوست التذكير (قبل المحاضرة بساعة)',
                'kind' => 'content',
                'offset' => 0,
                'idea' => "ينشر قبل المحاضرة بساعة للتذكير بالموعد.\nالمسار: Marketing_Graphics/Reminder_1Hour/Reminder_Post.png",
                'content_type' => 'post',
                'platforms' => ['facebook', 'instagram'],
            ],
            [
                'title' => 'مونتاج النسخة النهائية للمحاضرة',
                'kind' => 'video',
                'offset' => 2,
                'idea' => "التسجيل الخام في 05_Live_Recordings — النسخة النهائية بعد المونتاج.\nالمسار: Final_Lecture/Final_YouTube.mp4",
            ],
            [
                'title' => 'تصميم كفر يوتيوب (Thumbnail)',
                'kind' => 'design',
                'offset' => 2,
                'idea' => "الصورة المصغرة للفيديو على يوتيوب.\nالمسار: Final_Lecture/Youtube_Cover.png",
            ],
            [
                'title' => 'رفع المحاضرة على يوتيوب وحفظ الرابط',
                'kind' => 'publish',
                'offset' => 3,
                'idea' => "بعد الرفع: احفظ رابط الفيديو في ملف نصي.\nالمسار: Final_Lecture/youtube_link.txt",
            ],
            [
                'title' => 'قص مقاطع من المحاضرة (Lecture_Clips)',
                'kind' => 'content',
                'offset' => 4,
                'idea' => "مقاطع قصيرة من محتوى المحاضرة تنشر كريلز/شورتس.\nالمسار: Marketing_Clips/Lecture_Clips/",
                'content_type' => 'reels',
                'platforms' => ['instagram', 'tiktok', 'facebook'],
            ],
            [
                'title' => 'فيديو تشويقي بعد النشر (Teaser_After)',
                'kind' => 'video',
                'offset' => 4,
                'idea' => "لمحة عن المحاضرة توجّه لمشاهدتها كاملة على يوتيوب.\nالمسار: Marketing_Clips/Teasers/Teaser_After.mp4",
            ],
            [
                'title' => 'تصميم كفرات الريلز',
                'kind' => 'design',
                'offset' => 4,
                'idea' => "كفرات وعناصر بصرية لفيديوهات الريلز.\nالمسار: Marketing_Graphics/Reels_Design/",
            ],
            [
                'title' => 'جدولة ونشر المحتوى على السوشيال',
                'kind' => 'publish',
                'offset' => 5,
                'idea' => "انسخ المقاطع المطلوبة إلى 03_Social_Content/الشهر الحالي للجدولة — النسخة هناك مؤقتة، المكان الدائم فولدر المحاضرة.",
            ],
            [
                'title' => 'بوست آراء الحضور (Testimonials)',
                'kind' => 'content',
                'offset' => 6,
                'idea' => "ينشر بعد المحاضرة لو فيه ردود فعل من الحضور — يعزز الثقة للمرة الجاية.\nالمسار: Marketing_Graphics/Testimonials/Testimonial_Post.png",
                'content_type' => 'post',
                'platforms' => ['facebook', 'instagram'],
            ],
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
