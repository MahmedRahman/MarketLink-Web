<?php

namespace App\Services;

use App\Models\WorkTask;
use App\Models\WorkTaskFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use phpseclib3\Net\SFTP;
use RuntimeException;
use Throwable;

class AcademyNasStorage
{
    public function isEnabled(): bool
    {
        return (bool) config('academy_nas.enabled')
            && filled(config('academy_nas.host'))
            && filled(config('academy_nas.username'))
            && filled(config('academy_nas.password'));
    }

    /**
     * يرفع الملف المحلي إلى NAS ويعيد المسار النسبي تحت 03_Social_Content.
     *
     * @return array{relative_path: string, absolute_path: string, file_name: string}|null
     */
    public function syncWorkTaskFile(
        WorkTask $task,
        WorkTaskFile $workFile,
        ?string $batchFolder = null,
        ?int $index = null
    ): ?array {
        if (! $this->isEnabled()) {
            return null;
        }

        $localAbsolute = Storage::disk('public')->path($workFile->file_path);
        if (! is_file($localAbsolute)) {
            throw new RuntimeException('الملف المحلي غير موجود للمزامنة مع NAS');
        }

        $ext = strtolower((string) ($workFile->file_type ?: pathinfo($workFile->file_name, PATHINFO_EXTENSION)));
        $month = $this->monthFolder($task);
        $typeFolder = $this->typeFolder($task);
        $batchFolder = $batchFolder ?: $workFile->nas_folder;

        if ($batchFolder) {
            $fileName = $index
                ? $this->buildIndexedFileName($task, $ext, $index)
                : (string) $workFile->file_name;
            $relativeDir = $month.'/'.$typeFolder.'/'.$batchFolder;
        } else {
            $fileName = $this->buildFileName($task, $ext);
            $relativeDir = $month.'/'.$typeFolder;
        }

        $base = rtrim((string) config('academy_nas.base_path'), '/');
        $remoteDir = $base.'/'.$relativeDir;

        $sftp = $this->connect();

        try {
            if (! $sftp->is_dir($remoteDir) && ! $sftp->mkdir($remoteDir, -1, true)) {
                throw new RuntimeException('تعذر إنشاء مجلد NAS: '.$remoteDir);
            }

            $fileName = $this->uniqueRemoteName($sftp, $remoteDir, $fileName);
            $remotePath = $remoteDir.'/'.$fileName;

            if (! $sftp->put($remotePath, $localAbsolute, SFTP::SOURCE_LOCAL_FILE)) {
                throw new RuntimeException('فشل رفع الملف إلى NAS');
            }

            $relativePath = $relativeDir.'/'.$fileName;

            return [
                'relative_path' => $relativePath,
                'absolute_path' => $remotePath,
                'file_name' => $fileName,
            ];
        } finally {
            $sftp->disconnect();
        }
    }

    public function publicUrl(?string $relativePath): ?string
    {
        if (! $relativePath) {
            return null;
        }

        $publicBase = rtrim((string) config('academy_nas.public_base_url'), '/');
        if ($publicBase === '') {
            return null;
        }

        // File Browser root = shared → المسار النسبي من shared
        $base = rtrim((string) config('academy_nas.base_path'), '/');
        $sharedRoot = rtrim((string) config('academy_nas.shared_root'), '/');
        $fromShared = ltrim(Str::after($base, $sharedRoot).'/'.$relativePath, '/');

        return $publicBase.'/files/'.implode('/', array_map('rawurlencode', explode('/', $fromShared)));
    }

    public function monthFolder(WorkTask $task): string
    {
        $date = $task->publish_date ?? now();

        return $date->format('Y-m');
    }

    public function typeFolder(WorkTask $task): string
    {
        return match ($task->content_type) {
            'post' => '01_Posts',
            'carousel' => '02_Carousels',
            'reels' => '03_Reels',
            default => '04_Videos',
        };
    }

    public function contentTypeKey(WorkTask $task): string
    {
        return match ($task->content_type) {
            'post', 'carousel', 'reels' => $task->content_type,
            default => 'video',
        };
    }

    public function buildFileName(WorkTask $task, string $ext): string
    {
        $date = ($task->publish_date ?? now())->format('Ymd');
        $type = $this->contentTypeKey($task);
        $slug = $this->taskSlug($task);
        $ext = ltrim(strtolower($ext), '.');

        return "{$date}_{$type}_{$task->id}_{$slug}.{$ext}";
    }

    public function buildBatchFolderName(WorkTask $task): string
    {
        $date = ($task->publish_date ?? now())->format('Ymd');
        $type = $this->contentTypeKey($task);
        $slug = $this->taskSlug($task);

        return "{$date}_{$type}_{$task->id}_{$slug}";
    }

    public function buildIndexedFileName(WorkTask $task, string $ext, int $index): string
    {
        $slug = $this->taskSlug($task);
        $ext = ltrim(strtolower($ext), '.');

        return sprintf('%s_%02d.%s', $slug, $index, $ext);
    }

    public function taskSlug(WorkTask $task): string
    {
        $slug = Str::slug($task->title);
        if ($slug === '') {
            $slug = 'task-'.$task->id;
        }

        return Str::limit($slug, 40, '');
    }

    protected function uniqueRemoteName(SFTP $sftp, string $remoteDir, string $fileName): string
    {
        $path = $remoteDir.'/'.$fileName;
        if (! $sftp->file_exists($path)) {
            return $fileName;
        }

        $base = pathinfo($fileName, PATHINFO_FILENAME);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        for ($i = 2; $i <= 50; $i++) {
            $candidate = $base.'-'.$i.($ext ? '.'.$ext : '');
            if (! $sftp->file_exists($remoteDir.'/'.$candidate)) {
                return $candidate;
            }
        }

        return $base.'-'.Str::lower(Str::random(4)).($ext ? '.'.$ext : '');
    }

    protected function connect(): SFTP
    {
        $host = (string) config('academy_nas.host');
        $port = (int) config('academy_nas.port', 22);
        $user = (string) config('academy_nas.username');
        $password = (string) config('academy_nas.password');

        $sftp = new SFTP($host, $port);
        $sftp->setTimeout(45);
        if (! $sftp->login($user, $password)) {
            throw new RuntimeException('فشل الاتصال بسيرفر الملفات (NAS)');
        }

        return $sftp;
    }

    /**
     * مزامنة آمنة: تُسجّل الخطأ ولا ترمي للمستدعي إن طُلب.
     */
    public function syncQuietly(
        WorkTask $task,
        WorkTaskFile $workFile,
        ?string $batchFolder = null,
        ?int $index = null
    ): bool {
        try {
            $result = $this->syncWorkTaskFile($task, $workFile, $batchFolder, $index);
            if (! $result) {
                return false;
            }

            $workFile->update([
                'nas_path' => $result['relative_path'],
                'nas_synced_at' => now(),
                'file_name' => $result['file_name'],
                'nas_folder' => $batchFolder ?: $workFile->nas_folder,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::warning('Academy NAS sync failed', [
                'task_id' => $task->id,
                'file_id' => $workFile->id,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * ينقل الملف على NAS إلى فولدر deleted بدل الحذف النهائي.
     * المسار: {base}/deleted/{nas_path الأصلي}
     */
    public function archiveQuietly(WorkTaskFile $workFile): bool
    {
        if (! $this->isEnabled() || ! filled($workFile->nas_path)) {
            return false;
        }

        $nasPath = ltrim((string) $workFile->nas_path, '/');
        if (str_starts_with($nasPath, 'deleted/')) {
            return true; // بالفعل مؤرشف
        }

        try {
            $base = rtrim((string) config('academy_nas.base_path'), '/');
            $sourcePath = $base.'/'.$nasPath;
            $destRelative = 'deleted/'.$nasPath;
            $destPath = $base.'/'.$destRelative;
            $destDir = dirname($destPath);

            $sftp = $this->connect();

            try {
                if (! $sftp->file_exists($sourcePath)) {
                    Log::info('Academy NAS archive skipped — source missing', [
                        'file_id' => $workFile->id,
                        'nas_path' => $nasPath,
                    ]);

                    return false;
                }

                if (! $sftp->is_dir($destDir) && ! $sftp->mkdir($destDir, -1, true)) {
                    throw new RuntimeException('تعذر إنشاء مجلد deleted على NAS: '.$destDir);
                }

                $finalDest = $destPath;
                if ($sftp->file_exists($finalDest)) {
                    $dir = dirname($destPath);
                    $baseName = pathinfo($destPath, PATHINFO_FILENAME);
                    $ext = pathinfo($destPath, PATHINFO_EXTENSION);
                    $finalDest = $dir.'/'.$baseName.'-'.now()->format('YmdHis').($ext ? '.'.$ext : '');
                }

                // rename إن أمكن، وإلا copy عبر ملف مؤقت ثم احذف المصدر
                $moved = $sftp->rename($sourcePath, $finalDest);
                if (! $moved) {
                    $tmp = tempnam(sys_get_temp_dir(), 'nas_');
                    if (! $tmp || ! $sftp->get($sourcePath, $tmp)) {
                        if ($tmp) {
                            @unlink($tmp);
                        }
                        throw new RuntimeException('فشل نسخ الملف للأرشفة من NAS');
                    }
                    $ok = $sftp->put($finalDest, $tmp, SFTP::SOURCE_LOCAL_FILE);
                    @unlink($tmp);
                    if (! $ok) {
                        throw new RuntimeException('فشل رفع الملف إلى فولدر deleted على NAS');
                    }
                    $sftp->delete($sourcePath);
                }

                // نظّف فولدر الباتش لو فاضي
                $parentDir = dirname($sourcePath);
                if ($workFile->nas_folder && $sftp->is_dir($parentDir)) {
                    $entries = array_values(array_filter(
                        $sftp->nlist($parentDir) ?: [],
                        fn ($name) => $name !== '.' && $name !== '..'
                    ));
                    if ($entries === []) {
                        $sftp->rmdir($parentDir);
                    }
                }

                $newRelative = ltrim(Str::after($finalDest, $base.'/'), '/');
                $workFile->forceFill([
                    'nas_path' => $newRelative,
                    'nas_folder' => $workFile->nas_folder
                        ? 'deleted/'.$workFile->nas_folder
                        : $workFile->nas_folder,
                ])->save();

                Log::info('Academy NAS file archived to deleted', [
                    'file_id' => $workFile->id,
                    'from' => $nasPath,
                    'to' => $newRelative,
                ]);

                return true;
            } finally {
                $sftp->disconnect();
            }
        } catch (Throwable $e) {
            Log::warning('Academy NAS archive failed', [
                'file_id' => $workFile->id,
                'nas_path' => $workFile->nas_path,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * أرشفة ملف على NAS لمسار قديم بدون تعديل DB.
     * نستخدمها أثناء إعادة رفع ملف جديد لنفس التاسك بتسمية مختلفة.
     */
    public function archiveNasPathQuietly(?string $nasPath): bool
    {
        if (! $this->isEnabled() || ! filled($nasPath)) {
            return false;
        }

        $nasPath = ltrim((string) $nasPath, '/');

        if (str_starts_with($nasPath, 'deleted/')) {
            return true;
        }

        try {
            $base = rtrim((string) config('academy_nas.base_path'), '/');
            $sourcePath = $base.'/'.$nasPath;
            $destRelative = 'deleted/'.$nasPath;
            $destPath = $base.'/'.$destRelative;
            $destDir = dirname($destPath);

            $sftp = $this->connect();

            try {
                if (! $sftp->file_exists($sourcePath)) {
                    return false;
                }

                if (! $sftp->is_dir($destDir) && ! $sftp->mkdir($destDir, -1, true)) {
                    throw new RuntimeException('تعذر إنشاء مجلد deleted على NAS: '.$destDir);
                }

                $finalDest = $destPath;
                if ($sftp->file_exists($finalDest)) {
                    $dir = dirname($destPath);
                    $baseName = pathinfo($destPath, PATHINFO_FILENAME);
                    $ext = pathinfo($destPath, PATHINFO_EXTENSION);
                    $finalDest = $dir.'/'.$baseName.'-'.now()->format('YmdHis').($ext ? '.'.$ext : '');
                }

                // rename إن أمكن، وإلا copy عبر ملف مؤقت ثم احذف المصدر
                $moved = $sftp->rename($sourcePath, $finalDest);
                if (! $moved) {
                    $tmp = tempnam(sys_get_temp_dir(), 'nas_');
                    if (! $tmp || ! $sftp->get($sourcePath, $tmp)) {
                        if ($tmp) {
                            @unlink($tmp);
                        }
                        throw new RuntimeException('فشل نسخ الملف للأرشفة من NAS');
                    }

                    $ok = $sftp->put($finalDest, $tmp, SFTP::SOURCE_LOCAL_FILE);
                    @unlink($tmp);

                    if (! $ok) {
                        throw new RuntimeException('فشل رفع الملف إلى فولدر deleted على NAS');
                    }

                    $sftp->delete($sourcePath);
                }

                return true;
            } finally {
                $sftp->disconnect();
            }
        } catch (Throwable $e) {
            Log::warning('Academy NAS archive path failed', [
                'nas_path' => $nasPath,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * يحذف الملف من NAS إن وُجد مسار nas_path، ويمسح الفولدر لو فاضي.
     * @deprecated استخدم archiveQuietly بدل الحذف النهائي
     */
    public function deleteQuietly(WorkTaskFile $workFile): bool
    {
        return $this->archiveQuietly($workFile);
    }
}
