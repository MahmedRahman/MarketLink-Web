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
    public function syncWorkTaskFile(WorkTask $task, WorkTaskFile $workFile): ?array
    {
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
        $fileName = $this->buildFileName($task, $ext);

        $relativeDir = $month.'/'.$typeFolder;
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
        $slug = Str::slug($task->title);
        if ($slug === '') {
            $slug = 'task-'.$task->id;
        }
        $slug = Str::limit($slug, 40, '');
        $ext = ltrim(strtolower($ext), '.');

        return "{$date}_{$type}_{$task->id}_{$slug}.{$ext}";
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
        if (! $sftp->login($user, $password)) {
            throw new RuntimeException('فشل الاتصال بسيرفر الملفات (NAS)');
        }

        return $sftp;
    }

    /**
     * مزامنة آمنة: تُسجّل الخطأ ولا ترمي للمستدعي إن طُلب.
     */
    public function syncQuietly(WorkTask $task, WorkTaskFile $workFile): bool
    {
        try {
            $result = $this->syncWorkTaskFile($task, $workFile);
            if (! $result) {
                return false;
            }

            $workFile->update([
                'nas_path' => $result['relative_path'],
                'nas_synced_at' => now(),
                // احتفظ باسم الملف الأصلي للعرض المحلي إن لزم، لكن حدّث ليعكس الاسم المنظّم على NAS
                'file_name' => $result['file_name'],
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
}
