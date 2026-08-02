<?php

namespace App\Http\Controllers;

use App\Models\WorkActivity;
use App\Models\WorkTask;
use App\Models\WorkTaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class PublicWorkShareController extends Controller
{
    public function show(string $token): View
    {
        $activity = $this->findSharedActivity($token);
        $activity->load(['tasks.contentWriter', 'tasks.designer']);

        $pipelineStages = [];
        foreach (WorkTask::activePipelineStages() as $key => $label) {
            $stageTasks = $activity->tasks->where('pipeline_stage', $key)->values();
            $pipelineStages[] = [
                'key' => $key,
                'label' => $label,
                'icon' => match ($key) {
                    'planning' => 'pending_actions',
                    'design' => 'palette',
                    'ready_to_publish' => 'schedule_send',
                    'published' => 'check_circle',
                    default => 'edit_note',
                },
                'tasks' => $stageTasks,
                'count' => $stageTasks->count(),
            ];
        }

        $contentCounts = [
            'total' => $activity->tasks->count(),
            'post' => $activity->tasks->where('content_type', 'post')->count(),
            'reels' => $activity->tasks->where('content_type', 'reels')->count(),
            'carousel' => $activity->tasks->where('content_type', 'carousel')->count(),
        ];

        return view('public.work.show', [
            'activity' => $activity,
            'pipelineStages' => $pipelineStages,
            'contentCounts' => $contentCounts,
            'shareToken' => $token,
        ]);
    }

    /**
     * معرض كل ملفات التصميم (صور/فيديو) عبر الحملة كلها.
     * صور الكروسيل تتجمّع مع بعض في عنصر واحد.
     */
    public function showGallery(string $token): View
    {
        $activity = $this->findSharedActivity($token);
        $activity->load(['tasks.files', 'tasks.designer']);

        $items = collect();
        $imageCount = 0;
        $videoCount = 0;
        $carouselCount = 0;
        $videoPostCount = 0;

        $tasks = $activity->tasks
            ->sortBy(fn (WorkTask $task) => $task->gallerySortKey())
            ->values();

        foreach ($tasks as $task) {
            $media = $task->files
                ->filter(function (WorkTaskFile $file) {
                    if (! $file->isImage() && ! $file->isVideo()) {
                        return false;
                    }

                    return Storage::disk('public')->exists($file->file_path);
                })
                ->sortBy(fn (WorkTaskFile $file) => mb_strtolower((string) $file->file_name), SORT_NATURAL)
                ->values();

            if ($media->isEmpty()) {
                continue;
            }

            $imagesInTask = $media->filter(fn (WorkTaskFile $f) => $f->isImage())->count();
            $videosInTask = $media->filter(fn (WorkTaskFile $f) => $f->isVideo())->count();
            $imageCount += $imagesInTask;
            $videoCount += $videosInTask;

            $isCarouselType = $task->content_type === 'carousel' || $media->count() > 1;
            if ($isCarouselType) {
                $carouselCount++;
            }
            if ($videosInTask > 0) {
                $videoPostCount++;
            }

            // كارت واحد لكل تاسك: كروسيل أو صور متعددة لنفس البوست
            if ($media->count() > 1) {
                $items->push([
                    'type' => 'carousel',
                    'task' => $task,
                    'files' => $media,
                    'is_carousel_type' => $task->content_type === 'carousel',
                    'has_video' => $videosInTask > 0,
                    'image_count' => $imagesInTask,
                    'video_count' => $videosInTask,
                ]);

                continue;
            }

            $items->push([
                'type' => 'single',
                'task' => $task,
                'file' => $media->first(),
                'is_carousel_type' => false,
                'has_video' => $videosInTask > 0,
                'image_count' => $imagesInTask,
                'video_count' => $videosInTask,
            ]);
        }

        return view('public.work.gallery', [
            'activity' => $activity,
            'items' => $items,
            'shareToken' => $token,
            'galleryUrl' => route('public.work.gallery', $token),
            'designCount' => $items->count(),
            'imageCount' => $imageCount,
            'videoCount' => $videoCount,
            'carouselCount' => $carouselCount,
            'videoPostCount' => $videoPostCount,
        ]);
    }

    /**
     * تحميل الحملة كـ PDF للمراجعة السريعة (صور + كابشن) قبل النشر.
     */
    public function downloadGalleryPdf(string $token)
    {
        @ini_set('memory_limit', '512M');
        @ini_set('pcre.backtrack_limit', '5000000');
        @set_time_limit(180);

        $activity = $this->findSharedActivity($token);
        $activity->load(['tasks.files', 'tasks.designer']);

        $thumbs = app(\App\Services\WorkImageThumbnail::class);
        $posts = collect();

        $tasks = $activity->tasks
            ->sortBy(fn (WorkTask $task) => $task->gallerySortKey())
            ->values();

        foreach ($tasks as $task) {
            $media = $task->files
                ->filter(function (WorkTaskFile $file) {
                    if (! $file->isImage() && ! $file->isVideo()) {
                        return false;
                    }

                    return Storage::disk('public')->exists($file->file_path);
                })
                ->sortBy(fn (WorkTaskFile $file) => mb_strtolower((string) $file->file_name), SORT_NATURAL)
                ->values();

            if ($media->isEmpty()) {
                continue;
            }

            $images = [];
            foreach ($media as $file) {
                if ($file->isVideo()) {
                    $images[] = [
                        'kind' => 'video',
                        'name' => $file->file_name,
                        'path' => null,
                    ];

                    continue;
                }

                // صور أصغر على القرص مباشرة — بدون base64 عشان الحملات الكبيرة
                $path = $thumbs->jpegPath($file, 720, 70);
                if (! $path || ! is_file($path)) {
                    continue;
                }

                $images[] = [
                    'kind' => 'image',
                    'name' => $file->file_name,
                    'path' => $path,
                ];
            }

            if ($images === []) {
                continue;
            }

            $posts->push([
                'title' => $task->title,
                'post_number' => WorkTask::extractPostSequence($task->title),
                'type_label' => $task->content_type_label,
                'designer' => $task->designer?->name,
                'stage_label' => $task->pipeline_stage_label,
                'publish_label' => $task->publish_schedule_label,
                'platforms' => $task->platform_labels ?? [],
                'caption' => $task->caption,
                'tov' => $task->tov,
                'idea' => $task->idea,
                'images' => $images,
                'is_carousel' => $task->content_type === 'carousel' || $media->count() > 1,
            ]);
        }

        abort_if($posts->isEmpty(), 404, 'مفيش تصميمات للتصدير');

        $tempDir = storage_path('app/mpdf-tmp');
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'default_font' => 'dejavusans',
            'tempDir' => $tempDir,
        ]);
        $mpdf->SetDirectionality('rtl');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;
        $mpdf->SetTitle('مراجعة حملة — '.$activity->title);

        $styles = <<<'CSS'
<style>
body { font-family: dejavusans, sans-serif; font-size: 11pt; color: #0f172a; line-height: 1.55; }
.cover { text-align: center; padding: 28px 12px 18px; border-bottom: 2px solid #0d9488; margin-bottom: 18px; }
.cover-kicker { color: #0d9488; font-size: 10pt; font-weight: bold; margin-bottom: 6px; }
.cover h1 { font-size: 18pt; margin: 0 0 8px; }
.cover-meta { color: #64748b; font-size: 9pt; }
.stats { margin-top: 10px; font-size: 9pt; color: #334155; }
.post { page-break-inside: avoid; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin: 0 0 16px; }
.post-head { border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; margin-bottom: 10px; }
.post-title { font-size: 12pt; font-weight: bold; margin: 0 0 4px; }
.chips { color: #475569; font-size: 8.5pt; }
.label { color: #0d9488; font-size: 8.5pt; font-weight: bold; margin: 8px 0 3px; }
.caption { white-space: pre-wrap; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; font-size: 10pt; }
.muted { color: #94a3b8; font-size: 9pt; }
.img-wrap { text-align: center; margin: 8px 0; background: #f1f5f9; padding: 8px; border-radius: 6px; }
.img-wrap img { max-width: 100%; max-height: 280px; height: auto; }
.slide-label { font-size: 8pt; color: #64748b; margin-top: 3px; }
.video-note { background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 8px; border-radius: 6px; font-size: 9pt; margin: 6px 0; }
.footer { margin-top: 20px; text-align: center; color: #94a3b8; font-size: 8pt; border-top: 1px solid #e2e8f0; padding-top: 8px; }
</style>
CSS;

        // اكتب HTML على أجزاء عشان الحملات الكبيرة ما تكسرش mPDF
        $mpdf->WriteHTML($styles, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML(view('public.work.gallery-pdf-cover', [
            'activity' => $activity,
            'postsCount' => $posts->count(),
            'generatedAt' => now()->timezone(config('app.timezone', 'Africa/Cairo'))->format('Y/m/d H:i'),
        ])->render(), \Mpdf\HTMLParserMode::HTML_BODY);

        foreach ($posts as $post) {
            $mpdf->WriteHTML(
                view('public.work.gallery-pdf-post', ['post' => $post])->render(),
                \Mpdf\HTMLParserMode::HTML_BODY
            );
        }

        $mpdf->WriteHTML(
            '<div class="footer">MarketLink · ملف مراجعة داخلي قبل النشر</div>',
            \Mpdf\HTMLParserMode::HTML_BODY
        );

        $safeName = Str::slug($activity->title) ?: 'campaign';
        $filename = 'review-'.$safeName.'-'.now()->format('Ymd').'.pdf';

        return response($mpdf->Output($filename, \Mpdf\Output\Destination::STRING_RETURN), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * صفحة عامة لجدولة البوستات الجاهزة للنشر (بدون تسجيل).
     */
    public function showReadyToPublish(string $token): View
    {
        $activity = $this->findSharedActivity($token);
        $activity->load(['tasks.files', 'tasks.designer']);

        $tasks = $activity->tasks
            ->where('pipeline_stage', 'ready_to_publish')
            ->sortBy(function (WorkTask $task) {
                if ($task->publish_date) {
                    $time = $task->publish_time_short ?: '99:99';

                    return '0-'.$task->publish_date->format('Ymd').'-'.$time.'-'.$task->gallerySortKey();
                }

                return '1-'.$task->gallerySortKey();
            })
            ->values();

        return view('public.work.ready-to-publish', [
            'activity' => $activity,
            'tasks' => $tasks,
            'shareToken' => $token,
            'pageUrl' => route('public.work.ready-to-publish', $token),
        ]);
    }

    public function updatePublishSchedule(Request $request, string $token, WorkTask $task)
    {
        $activity = $this->findSharedActivity($token);
        abort_unless((int) $task->work_activity_id === (int) $activity->id, 404);
        abort_unless($task->pipeline_stage === 'ready_to_publish', 422, 'التاسك مش في مرحلة جاهز للنشر');

        $validated = $request->validate([
            'publish_date' => 'nullable|date',
            'publish_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
        ]);

        $date = $validated['publish_date'] ?? null;
        $time = $validated['publish_time'] ?? null;
        if ($time) {
            $time = strlen($time) === 5 ? $time.':00' : $time;
        }
        if (! $date) {
            $time = null;
        }

        $beforeDate = $task->publish_date?->format('Y-m-d');
        $beforeTime = $task->publish_time_short;
        $task->update([
            'publish_date' => $date,
            'publish_time' => $time,
        ]);
        $task->refresh();

        if ($beforeDate !== $task->publish_date?->format('Y-m-d') || $beforeTime !== $task->publish_time_short) {
            $toLabel = $task->publish_schedule_label ?? 'بدون موعد';
            $fromLabel = $beforeDate
                ? ($beforeDate.($beforeTime ? ' · '.$beforeTime : ''))
                : 'بدون موعد';
            $task->logEvent(
                'publish_schedule_updated',
                'تم تحديث موعد النشر من الرابط العام من «'.$fromLabel.'» إلى «'.$toLabel.'»',
                'publish_date',
                $fromLabel,
                $toLabel
            );
        }

        $message = $task->publish_date
            ? 'تم حفظ موعد النشر: '.$task->publish_schedule_label
            : 'تم مسح موعد النشر';

        return response()->json([
            'success' => true,
            'message' => $message,
            'publish_date' => $task->publish_date?->format('Y-m-d'),
            'publish_time' => $task->publish_time_short,
            'label' => $task->publish_schedule_label,
        ]);
    }

    public function showTask(string $token, WorkTask $task): View
    {
        $activity = $this->findSharedActivity($token);
        abort_unless($task->work_activity_id === $activity->id, 404);

        $task->load(['contentWriter', 'designer', 'assignedEmployee', 'files']);

        return view('public.work.task', [
            'activity' => $activity,
            'task' => $task,
            'shareToken' => $token,
            'cardShareUrl' => route('public.work.task', [$token, $task]),
        ]);
    }

    public function showFile(Request $request, string $token, WorkTask $task, WorkTaskFile $file): StreamedResponse|BinaryFileResponse
    {
        $activity = $this->findSharedActivity($token);
        abort_unless($task->work_activity_id === $activity->id, 404);
        abort_unless($file->work_task_id === $task->id, 404);
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        $forceDownload = $request->boolean('download');

        // معاينة سريعة للمعرض (مصغّرة + كاش طويل)
        if (! $forceDownload && $file->isImage() && ($request->filled('w') || $request->boolean('thumb'))) {
            $maxEdge = (int) ($request->input('w') ?: 480);
            $quality = (int) ($request->input('q') ?: ($maxEdge >= 700 ? 84 : 74));
            $thumb = app(\App\Services\WorkImageThumbnail::class)->respond($file, $maxEdge, $quality);
            if ($thumb) {
                return $thumb;
            }
        }

        $disposition = $forceDownload
            ? 'attachment'
            : (($file->isImage() || $file->isPdf()) ? 'inline' : 'attachment');

        $headers = [];
        if (! $forceDownload && ($file->isImage() || $file->isVideo())) {
            $headers['Cache-Control'] = 'public, max-age=86400';
        }

        return Storage::disk('public')->response(
            $file->file_path,
            $file->file_name,
            $headers,
            $disposition
        );
    }

    public function downloadAllFiles(string $token, WorkTask $task): BinaryFileResponse
    {
        $activity = $this->findSharedActivity($token);
        abort_unless($task->work_activity_id === $activity->id, 404);

        $task->load('files');
        abort_unless($task->files->isNotEmpty(), 404);
        abort_unless(class_exists(ZipArchive::class), 500, 'ZipArchive غير متاح على السيرفر');

        $slug = Str::slug($task->title) ?: ('task-'.$task->id);
        $zipName = $slug.'-design-files.zip';
        $tmpDir = storage_path('app/tmp');
        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }
        $tmpPath = $tmpDir.'/'.uniqid('share_', true).'-'.$zipName;

        $zip = new ZipArchive;
        if ($zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'تعذر إنشاء ملف ZIP');
        }

        $usedNames = [];
        $added = 0;
        foreach ($task->files as $index => $file) {
            if (! Storage::disk('public')->exists($file->file_path)) {
                continue;
            }

            $entryName = $file->file_name ?: ('file-'.($index + 1));
            $base = pathinfo($entryName, PATHINFO_FILENAME) ?: 'file';
            $ext = pathinfo($entryName, PATHINFO_EXTENSION);
            $candidate = $entryName;
            $i = 2;
            while (isset($usedNames[Str::lower($candidate)])) {
                $candidate = $base.'-'.$i.($ext ? '.'.$ext : '');
                $i++;
            }
            $usedNames[Str::lower($candidate)] = true;

            $zip->addFile(Storage::disk('public')->path($file->file_path), $candidate);
            $added++;
        }

        $zip->close();

        if ($added === 0 || ! is_file($tmpPath)) {
            if (is_file($tmpPath)) {
                @unlink($tmpPath);
            }
            abort(404, 'لا توجد ملفات للتحميل');
        }

        return response()->download($tmpPath, $zipName)->deleteFileAfterSend(true);
    }

    private function findSharedActivity(string $token): WorkActivity
    {
        return WorkActivity::where('share_token', $token)->firstOrFail();
    }
}
