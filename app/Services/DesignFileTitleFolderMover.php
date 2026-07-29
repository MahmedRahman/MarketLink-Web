<?php

namespace App\Services;

use App\Models\WorkTask;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DesignFileTitleFolderMover
{
    public function __construct(
        private readonly AcademyNasStorage $nas
    ) {}

    /**
     * ينقل ملفات التصميم المرتبطة بتاسك إلى فولدر/مسار NAS مبني على عنوان التاسك الحالي.
     *
     * - للـ batch (أكثر من ملف): ينقل فولدر الـ local (work-tasks/{task.id}/{nas_folder})
     *   لو متاح، ويعمل NAS re-sync للفولدر الجديد حسب title.
     * - للملف الواحد: يعمل NAS re-sync لتحديث nas_path + file_name بناءً على title.
     *
     * @return array{files:int,nas_synced:int,nas_archived:int,local_moved:int}
     */
    public function moveDesignFilesToCurrentTitle(WorkTask $task): array
    {
        $files = $task->files()
            ->get()
            ->values();

        if ($files->isEmpty()) {
            return [
                'files' => 0,
                'nas_synced' => 0,
                'nas_archived' => 0,
                'local_moved' => 0,
            ];
        }

        $localMoved = 0;
        $nasSynced = 0;
        $nasArchived = 0;

        // خريطة batchId => جديد (nas folder name حسب title الجديد)
        $newBatchFolders = [];

        // 1) Local move للـ batch folders (اختياري حسب وجود مجلد فعلي)
        $batchIds = $files
            ->pluck('upload_batch')
            ->filter(fn ($v) => filled($v))
            ->unique()
            ->values();

        foreach ($batchIds as $batchId) {
            /** @var WorkTaskFile|null $first */
            $first = $files->firstWhere('upload_batch', $batchId);
            if (! $first || ! filled($first->nas_folder)) {
                continue;
            }

            $oldFolder = (string) $first->nas_folder;
            $newFolder = $this->nas->buildBatchFolderName($task);
            $newBatchFolders[$batchId] = $newFolder;

            if ($oldFolder === $newFolder) {
                continue;
            }

            $disk = Storage::disk('public');
            $taskId = (int) $task->id;

            $oldDir = "work-tasks/{$taskId}/{$oldFolder}";
            $newDir = "work-tasks/{$taskId}/{$newFolder}";
            $paths = $disk->files($oldDir) ?: [];
            if ($paths === []) {
                continue; // الباتش موجود في DB بس local مش موجود بنفس البنية
            }

            // انقل كل الملفات تحت oldDir وحدّث file_path لـ DB.
            $mapByPath = $files
                ->where('upload_batch', $batchId)
                ->keyBy('file_path');

            foreach ($paths as $oldPath) {
                $relative = Str::after($oldPath, rtrim($oldDir, '/').'/');
                $newPath = rtrim($newDir, '/').'/'.$relative;

                $disk->makeDirectory(dirname($newPath));
                $disk->move($oldPath, $newPath);

                $file = $mapByPath->get($oldPath);
                if ($file) {
                        $file->forceFill([
                            'file_path' => $newPath,
                            'nas_folder' => $newFolder,
                        ])->save();
                }
            }

            $localMoved++;
        }

        // 2) NAS re-sync + أرشفة المسار القديم بعد نجاح النسخ
        if ($this->nas->isEnabled()) {
            foreach ($files as $file) {
                $oldNasPath = $file->nas_path;

                $batchFolderForSync = null;
                $indexForSync = null;

                if (filled($file->upload_batch) && filled($file->nas_folder)) {
                    $batchId = $file->upload_batch;
                    $batchFolderForSync = $newBatchFolders[$batchId] ?? $this->nas->buildBatchFolderName($task);

                    // اعمل index ثابت من ترتيب ملفات نفس الباتش (حسب id).
                    $batchFiles = $files
                        ->where('upload_batch', $batchId)
                        ->sortBy('id')
                        ->values();
                    $pos = $batchFiles->search(fn ($f) => (int) $f->id === (int) $file->id);
                    $indexForSync = is_int($pos) ? ($pos + 1) : null;
                }

                try {
                    $ok = $this->nas->syncQuietly($task, $file, $batchFolderForSync, $indexForSync);
                    if ($ok) {
                        $nasSynced++;
                        $archived = $this->nas->archiveNasPathQuietly($oldNasPath);
                        if ($archived) {
                            $nasArchived++;
                        }
                    }
                } catch (Throwable) {
                    // syncQuietly نفسها بتسجل، هنا منعا لأي أخطاء غير متوقعة.
                }
            }
        }

        return [
            'files' => $files->count(),
            'nas_synced' => $nasSynced,
            'nas_archived' => $nasArchived,
            'local_moved' => $localMoved,
        ];
    }
}

