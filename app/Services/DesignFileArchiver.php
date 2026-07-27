<?php

namespace App\Services;

use App\Models\WorkActivity;
use App\Models\WorkTask;
use App\Models\WorkTaskFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DesignFileArchiver
{
    public function __construct(
        private readonly AcademyNasStorage $nas
    ) {}

    /**
     * ينقل الملف محليًا إلى deleted/ ويعمل archive على NAS، ثم يحذف سجل DB إن طُلب.
     *
     * @return array{local: bool, nas: bool}
     */
    public function archiveFile(WorkTaskFile $file, bool $deleteRecord = true): array
    {
        $nasOk = $this->nas->archiveQuietly($file);
        $localOk = $this->archiveLocal($file);

        if ($deleteRecord) {
            $file->delete();
        }

        return ['local' => $localOk, 'nas' => $nasOk];
    }

    public function archiveTask(WorkTask $task): int
    {
        $task->loadMissing('files');
        $count = 0;
        foreach ($task->files as $file) {
            $this->archiveFile($file, true);
            $count++;
        }
        $task->delete();

        return $count;
    }

    public function archiveActivity(WorkActivity $activity): array
    {
        $activity->load(['tasks.files']);
        $files = 0;
        $tasks = $activity->tasks->count();

        foreach ($activity->tasks as $task) {
            foreach ($task->files as $file) {
                $this->archiveFile($file, true);
                $files++;
            }
        }

        // cascade هيحذف التاسكات والسجلات؛ نمسّح النشاط صراحة بعد أرشفة الملفات
        $activity->delete();

        return ['tasks' => $tasks, 'files' => $files];
    }

    protected function archiveLocal(WorkTaskFile $file): bool
    {
        $path = (string) $file->file_path;
        if ($path === '' || str_starts_with($path, 'deleted/')) {
            return false;
        }

        if (! Storage::disk('public')->exists($path)) {
            return false;
        }

        $dest = 'deleted/'.$path;
        if (Storage::disk('public')->exists($dest)) {
            $dir = dirname($dest);
            $name = pathinfo($dest, PATHINFO_FILENAME);
            $ext = pathinfo($dest, PATHINFO_EXTENSION);
            $dest = $dir.'/'.$name.'-'.now()->format('YmdHis').($ext ? '.'.$ext : '');
        }

        try {
            Storage::disk('public')->makeDirectory(dirname($dest));
            Storage::disk('public')->move($path, $dest);
            $file->forceFill(['file_path' => $dest])->save();

            $this->cleanupEmptyLocalDir(dirname($path));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Local design file archive failed', [
                'file_id' => $file->id,
                'path' => $path,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function cleanupEmptyLocalDir(string $dir): void
    {
        if ($dir === '.' || $dir === '' || ! str_contains($dir, 'work-tasks')) {
            return;
        }

        $absolute = Storage::disk('public')->path($dir);
        if (! is_dir($absolute)) {
            return;
        }

        $remaining = array_values(array_filter(scandir($absolute) ?: [], fn ($n) => $n !== '.' && $n !== '..'));
        if ($remaining === []) {
            @rmdir($absolute);
            $parent = dirname($dir);
            if ($parent !== $dir) {
                $this->cleanupEmptyLocalDir($parent);
            }
        }
    }
}
