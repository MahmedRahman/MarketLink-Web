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

            $imageCount += $media->filter(fn (WorkTaskFile $f) => $f->isImage())->count();
            $videoCount += $media->filter(fn (WorkTaskFile $f) => $f->isVideo())->count();

            // كارت واحد لكل تاسك: كروسيل أو صور متعددة لنفس البوست
            if ($media->count() > 1) {
                $items->push([
                    'type' => 'carousel',
                    'task' => $task,
                    'files' => $media,
                    'is_carousel_type' => $task->content_type === 'carousel',
                ]);

                continue;
            }

            $items->push([
                'type' => 'single',
                'task' => $task,
                'file' => $media->first(),
            ]);
        }

        return view('public.work.gallery', [
            'activity' => $activity,
            'items' => $items,
            'shareToken' => $token,
            'galleryUrl' => route('public.work.gallery', $token),
            'imageCount' => $imageCount,
            'videoCount' => $videoCount,
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
