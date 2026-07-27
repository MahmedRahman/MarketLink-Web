<?php

namespace App\Services;

use App\Models\WorkTask;
use App\Models\WorkTaskFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class DesignFileUploader
{
    public function __construct(
        private readonly AcademyNasStorage $nas
    ) {}

    /**
     * @param  array<int, UploadedFile>  $files
     * @return array{count: int, batch: ?string, folder: ?string}
     */
    public function uploadMany(
        WorkTask $task,
        array $files,
        string $assetKind,
        ?string $description = null,
        ?int $uploadedBy = null
    ): array {
        $files = array_values(array_filter($files));
        if ($files === []) {
            throw ValidationException::withMessages(['files' => 'اختر ملفًا واحدًا على الأقل']);
        }

        $allowed = match ($assetKind) {
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'video' => ['mp4', 'mov', 'webm', 'm4v'],
            'pdf' => ['pdf'],
            default => [],
        };

        foreach ($files as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = (string) $file->getMimeType();

            if (! in_array($ext, $allowed, true)) {
                throw ValidationException::withMessages([
                    'files' => 'امتداد الملف غير مناسب: '.$file->getClientOriginalName(),
                ]);
            }

            $mimeOk = match ($assetKind) {
                'image' => str_starts_with($mime, 'image/'),
                'video' => str_starts_with($mime, 'video/') || in_array($mime, ['application/octet-stream'], true),
                'pdf' => in_array($mime, ['application/pdf', 'application/octet-stream'], true),
                default => false,
            };
            if (! $mimeOk) {
                throw ValidationException::withMessages([
                    'files' => 'نوع الملف غير مدعوم: '.$file->getClientOriginalName(),
                ]);
            }
        }

        $isBatch = count($files) > 1;
        $batch = $isBatch ? (string) Str::uuid() : null;
        $folderName = $isBatch ? $this->nas->buildBatchFolderName($task) : null;

        $createdIds = [];
        $index = 0;
        foreach ($files as $file) {
            $index++;
            $ext = strtolower($file->getClientOriginalExtension());

            $dir = 'work-tasks/'.$task->id.($folderName ? '/'.$folderName : '');
            $storedName = $isBatch
                ? $this->nas->buildIndexedFileName($task, $ext, $index)
                : null;

            if ($storedName) {
                $path = $file->storeAs($dir, $storedName, 'public');
                $displayName = $storedName;
            } else {
                $path = $file->store($dir, 'public');
                $displayName = $file->getClientOriginalName();
            }

            $workFile = WorkTaskFile::create([
                'work_task_id' => $task->id,
                'file_name' => $displayName,
                'file_path' => $path,
                'file_type' => $ext,
                'asset_kind' => $assetKind,
                'file_size' => $file->getSize(),
                'uploaded_by' => $uploadedBy,
                'description' => $description,
                'upload_batch' => $batch,
                'nas_folder' => $folderName,
            ]);
            $createdIds[] = $workFile->id;
        }

        if ($this->nas->isEnabled() && $createdIds !== []) {
            $taskId = $task->id;
            $folder = $folderName;
            dispatch(function () use ($taskId, $createdIds, $folder) {
                $taskModel = WorkTask::query()->find($taskId);
                if (! $taskModel) {
                    return;
                }
                $nas = app(AcademyNasStorage::class);
                foreach ($createdIds as $i => $fileId) {
                    $fileModel = WorkTaskFile::query()->find($fileId);
                    if ($fileModel) {
                        $nas->syncQuietly($taskModel, $fileModel, $folder, $i + 1);
                    }
                }
            })->afterResponse();
        }

        Log::info('Design files uploaded', [
            'task_id' => $task->id,
            'count' => count($createdIds),
            'batch' => $batch,
            'folder' => $folderName,
        ]);

        return [
            'count' => count($createdIds),
            'batch' => $batch,
            'folder' => $folderName,
        ];
    }
}
