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
        'folder_id',
        'created_by',
        'title',
        'type',
        'description',
        'lecturer_name',
        'lecture_goals',
        'event_date',
        'lecture_time',
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

    public function folder(): BelongsTo
    {
        return $this->belongsTo(WorkFolder::class, 'folder_id');
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

    public function getPublicGalleryUrlAttribute(): ?string
    {
        if (! $this->share_token) {
            return null;
        }

        return route('public.work.gallery', $this->share_token);
    }

    public function getPublicReadyToPublishUrlAttribute(): ?string
    {
        if (! $this->share_token) {
            return null;
        }

        return route('public.work.ready-to-publish', $this->share_token);
    }

    public function publicTaskUrl(WorkTask|int $task): ?string
    {
        if (! $this->share_token) {
            return null;
        }

        $taskId = $task instanceof WorkTask ? $task->id : $task;

        return route('public.work.task', [$this->share_token, $taskId]);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::types()[$this->type]
            ?? match ($this->type) {
                // توافق مع السجلات القديمة
                'free_lecture' => 'محاضرة لايف مجانية',
                default => 'أخرى',
            };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'live_lecture', 'free_lecture' => 'live_tv',
            'live_lecture_paid' => 'paid',
            'paid_round' => 'workspace_premium',
            'educational' => 'menu_book',
            default => 'category',
        };
    }

    /**
     * هل النشاط محاضرة لايف (مجانية/مدفوعة) تتبع دليل تنظيم ملفات المحاضرة؟
     */
    public function getIsLectureAttribute(): bool
    {
        return in_array($this->type, ['live_lecture', 'live_lecture_paid', 'free_lecture'], true);
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

    public function getMonthLabelAttribute(): string
    {
        $date = $this->event_date ?? $this->created_at;
        if (! $date) {
            return '';
        }

        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
        ];

        return ($months[(int) $date->format('n')] ?? $date->format('m')).' '.$date->format('Y');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'تخطيط',
            'in_progress' => 'قيد التنفيذ',
            'done' => 'منجز',
            'cancelled' => 'ملغي',
            'archived' => 'أرشيف',
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
            'archived' => 'gray',
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
            'live_lecture' => 'محاضرة لايف مجانية',
            'live_lecture_paid' => 'محاضرة لايف مدفوعة',
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
     * يحقن الكابي (caption/tov/idea) من بيانات المحاضرة إن وُجدت.
     *
     * @param  array{title?:string,lecturer_name?:string,event_date?:string,lecture_time?:string,lecture_goals?:string,description?:string}  $context
     */
    public static function lectureTaskTemplate(array $context = []): array
    {
        $title = trim((string) ($context['title'] ?? 'المحاضرة'));
        $lecturer = trim((string) ($context['lecturer_name'] ?? ''));
        $goals = trim((string) ($context['lecture_goals'] ?? ''));
        $time = trim((string) ($context['lecture_time'] ?? ''));
        $dateLabel = ! empty($context['event_date'])
            ? (string) $context['event_date']
            : 'قريبًا';
        $when = $time !== '' ? ($dateLabel.' — '.$time) : $dateLabel;
        $withLecturer = $lecturer !== '' ? 'مع '.$lecturer : '';
        $goalsLine = $goals !== '' ? $goals : 'اكتساب معرفة عملية قابلة للتطبيق مباشرة بعد الحضور.';

        $tov = "نبرة أكاديمية دافئة، واضحة، ومحفّزة.\nخاطب الجمهور بصيغة «أنت»، وركّز على الفائدة العملية.\nبدون مبالغة إعلانية — ثقة + قيمة + دعوة واضحة للحضور.";

        $announcementCaption = implode("\n", array_filter([
            "محاضرة لايف مجانية {$withLecturer}".($withLecturer ? '' : ''),
            "📌 {$title}",
            '',
            'هتتكلم عن:',
            $goalsLine,
            '',
            "🗓 الموعد: {$when}",
            '',
            'الحضور مجاني — احجز مكانك الآن وكن جاهزًا بأسئلتك.',
            '',
            '#محاضرة_مجانية #تطوير #لايف',
        ]));

        $reminderCaption = implode("\n", array_filter([
            'تذكير ⏰',
            "محاضرتنا المجانية «{$title}» بكرة في نفس الموعد.",
            $lecturer !== '' ? "المحاضر: {$lecturer}" : null,
            "الموعد: {$when}",
            '',
            'احجز وقتك من دلوقتي وكن جاهزًا بأسئلتك — باقي 24 ساعة.',
            '',
            '#تذكير #لايف_مجاني',
        ]));

        $clipsCaption = implode("\n", array_filter([
            "مقطع عرضي من محاضرة «{$title}»",
            $lecturer !== '' ? "مع {$lecturer}" : null,
            '',
            'لو حابب تشوف المحاضرة كاملة — اللينك في البايو / الكومنتس.',
            '',
            '#مقاطع #تعليم #محاضرة',
        ]));

        $testimonialCaption = implode("\n", array_filter([
            "انطباعات الحضور بعد محاضرة «{$title}»",
            $lecturer !== '' ? "مع {$lecturer}" : null,
            '',
            'شكرًا لكل اللي حضر وشارك — رأيكم بيحفّزنا نقدّم محتوى أقوى كل مرة.',
            '',
            'لو فاتك اللقاء، تقدر تتابعه من رفع المحاضرة على الموقع ويوتيوب.',
            '',
            '#آراء_الحضور #محاضرة_مجانية',
        ]));

        $brief = implode("\n", array_filter([
            "عنوان المحاضرة: {$title}",
            $lecturer !== '' ? "المحاضر: {$lecturer}" : null,
            "الموعد: {$when}",
            "الأهداف:\n{$goalsLine}",
        ]));

        return [
            [
                'title' => 'تصميم بنر الموقع للمحاضرة',
                'kind' => 'design',
                'offset' => -5,
                'idea' => $brief."\n\nصمّم بنر صفحة المحاضرة على موقع الأكاديمية: عنوان واضح، اسم المحاضر، الموعد، ودعوة للحضور/المشاهدة.\nالأبعاد حسب مقاس بنر الموقع المعتمد.",
            ],
            [
                'title' => 'فيديو تشويقي قبل المحاضرة (Teaser_Before)',
                'kind' => 'video',
                'offset' => -2,
                'idea' => $brief."\n\nفيديو قصير (15–30 ثانية) يشجع الحضور: عنوان قوي + وعد بالقيمة + الموعد.\nالمسار: Marketing_Clips/Teasers/Teaser_Before.mp4",
                'tov' => $tov,
                'caption' => "تشويقة سريعة لمحاضرة «{$title}» — الموعد {$when}. احجز مكانك مجانًا.",
            ],
            [
                'title' => 'رفع المحاضرة المجانية على الموقع',
                'kind' => 'publish',
                'offset' => 3,
                'idea' => $brief."\n\nارفع المحاضرة المجانية على موقع الأكاديمية مع العنوان والوصف والأهداف والبنر، واربط فيديو يوتيوب إن وجد.\nتأكد إن الصفحة ظاهرة للزوار وأن رابط المشاركة جاهز للنشر على السوشيال.",
                'tov' => $tov,
                'caption' => implode("\n", array_filter([
                    "المحاضرة المجانية «{$title}» بقت متاحة على الموقع 🎓",
                    $lecturer !== '' ? "مع {$lecturer}" : null,
                    '',
                    'تقدر تشوفها في أي وقت من لينك الموقع.',
                    '',
                    '#محاضرة_مجانية #على_الموقع',
                ])),
            ],
            [
                'title' => 'تصميم بوست إعلان المحاضرة',
                'kind' => 'content',
                'offset' => -3,
                'idea' => $brief."\n\nينشر قبل المحاضرة بأيام لإعلان الموعد والموضوع.\nالمسار: Marketing_Graphics/Announcement/Announcement_Post.png",
                'tov' => $tov,
                'caption' => $announcementCaption,
                'content_type' => 'post',
                'platforms' => ['facebook', 'instagram'],
            ],
            [
                'title' => 'تصميم بوست التذكير (قبل المحاضرة بـ 24 ساعة)',
                'kind' => 'content',
                'offset' => -1,
                'idea' => $brief."\n\nينشر قبل المحاضرة بـ 24 ساعة للتذكير بالموعد.\nالمسار: Marketing_Graphics/Reminder_24Hour/Reminder_Post.png",
                'tov' => $tov,
                'caption' => $reminderCaption,
                'content_type' => 'post',
                'platforms' => ['facebook', 'instagram'],
            ],
            [
                'title' => 'تصميم كفر يوتيوب (Thumbnail)',
                'kind' => 'design',
                'offset' => 2,
                'idea' => $brief."\n\nالصورة المصغرة للفيديو على يوتيوب — عنوان واضح + اسم المحاضر إن وجد.\nالمسار: Final_Lecture/Youtube_Cover.png",
            ],
            [
                'title' => 'مونتاج النسخة النهائية للمحاضرة',
                'kind' => 'video',
                'offset' => 2,
                'idea' => $brief."\n\nالتسجيل الخام في 05_Live_Recordings — النسخة النهائية بعد المونتاج.\nالمسار: Final_Lecture/Final_YouTube.mp4",
            ],
            [
                'title' => 'رفع المحاضرة على يوتيوب وحفظ الرابط',
                'kind' => 'publish',
                'offset' => 3,
                'idea' => $brief."\n\nبعد الرفع: احفظ رابط الفيديو في ملف نصي.\nالمسار: Final_Lecture/youtube_link.txt",
            ],
            [
                'title' => 'قص مقاطع من المحاضرة بشكل عرضي (Lecture_Clips)',
                'kind' => 'video',
                'offset' => 4,
                'idea' => $brief."\n\nقص مقاطع قصيرة من المحاضرة بصيغة عرضية (Landscape / 16:9) — مش ريلز عمودي.\nمناسب ليوتيوب/فيسبوك أو أي منصة تدعم العرض الأفقي.\nالمسار: Marketing_Clips/Lecture_Clips/Landscape/",
                'tov' => $tov,
                'caption' => $clipsCaption,
                'platforms' => ['facebook'],
            ],
            [
                'title' => 'قص مقاطع من المحاضرة بشكل طولي (Lecture_Clips)',
                'kind' => 'video',
                'offset' => 4,
                'idea' => $brief."\n\nقص مقاطع قصيرة من المحاضرة بصيغة طولية (Portrait / 9:16) للريلز والشورتس وتيك توك.\nالمسار: Marketing_Clips/Lecture_Clips/Portrait/",
                'tov' => $tov,
                'caption' => implode("\n", array_filter([
                    "مقطع طولي من محاضرة «{$title}»",
                    $lecturer !== '' ? "مع {$lecturer}" : null,
                    '',
                    'لو حابب تشوف المحاضرة كاملة — اللينك في البايو / الكومنتس.',
                    '',
                    '#مقاطع #تعليم #ريلز',
                ])),
                'content_type' => 'reels',
                'platforms' => ['instagram', 'tiktok', 'facebook'],
            ],
            [
                'title' => 'بوست آراء الحضور (Testimonials)',
                'kind' => 'content',
                'offset' => 6,
                'idea' => $brief."\n\nينشر بعد المحاضرة لو فيه ردود فعل من الحضور — يعزز الثقة للمرة الجاية.\nالمسار: Marketing_Graphics/Testimonials/Testimonial_Post.png",
                'tov' => $tov,
                'caption' => $testimonialCaption,
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
            'archived' => 'أرشيف',
        ];
    }
}
