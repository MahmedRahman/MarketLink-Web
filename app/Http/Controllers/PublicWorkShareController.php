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
        foreach (WorkTask::pipelineStages() as $key => $label) {
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
        $activity->load(['tasks.files']);

        $items = collect();
        $imageCount = 0;
        $videoCount = 0;

        foreach ($activity->tasks->sortBy('order') as $task) {
            $media = $task->files
                ->filter(function (WorkTaskFile $file) {
                    if (! $file->isImage() && ! $file->isVideo()) {
                        return false;
                    }

                    return Storage::disk('public')->exists($file->file_path);
                })
                ->values();

            if ($media->isEmpty()) {
                continue;
            }

            $imageCount += $media->filter(fn (WorkTaskFile $f) => $f->isImage())->count();
            $videoCount += $media->filter(fn (WorkTaskFile $f) => $f->isVideo())->count();

            $isCarousel = $task->content_type === 'carousel' && $media->count() > 1;

            if ($isCarousel) {
                $items->push([
                    'type' => 'carousel',
                    'task' => $task,
                    'files' => $media,
                ]);

                continue;
            }

            foreach ($media as $file) {
                $items->push([
                    'type' => 'single',
                    'task' => $task,
                    'file' => $file,
                ]);
            }
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

    public function showFile(Request $request, string $token, WorkTask $task, WorkTaskFile $file): StreamedResponse
    {
        $activity = $this->findSharedActivity($token);
        abort_unless($task->work_activity_id === $activity->id, 404);
        abort_unless($file->work_task_id === $task->id, 404);
        abort_unless(Storage::disk('public')->exists($file->file_path), 404);

        $forceDownload = $request->boolean('download');
        $disposition = $forceDownload
            ? 'attachment'
            : (($file->isImage() || $file->isPdf()) ? 'inline' : 'attachment');

        return Storage::disk('public')->response(
            $file->file_path,
            $file->file_name,
            [],
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
