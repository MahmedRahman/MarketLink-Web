<?php

namespace App\Services;

use App\Models\WorkTaskFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WorkImageThumbnail
{
    /**
     * يرجّع صورة مصغّرة (JPEG) مع كاش على القرص.
     * لو GD مش متاح أو الملف مش صورة قابلة للمعالجة، يرجع null.
     */
    public function respond(WorkTaskFile $file, int $maxEdge = 480, int $quality = 72): ?BinaryFileResponse
    {
        if (! $file->isImage() || ! extension_loaded('gd')) {
            return null;
        }

        $maxEdge = max(96, min(1600, $maxEdge));
        $quality = max(40, min(90, $quality));

        if (! Storage::disk('public')->exists($file->file_path)) {
            return null;
        }

        $srcPath = Storage::disk('public')->path($file->file_path);
        $mtime = @filemtime($srcPath) ?: 0;
        $thumbRel = 'work-thumbs/'.md5($file->id.'|'.$file->file_path.'|'.$mtime.'|'.$maxEdge.'|'.$quality).'.jpg';
        $thumbPath = Storage::disk('public')->path($thumbRel);

        if (! is_file($thumbPath)) {
            if (! $this->createJpegThumb($srcPath, $thumbPath, $maxEdge, $quality)) {
                return null;
            }
        }

        return response()->file($thumbPath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    private function createJpegThumb(string $srcPath, string $destPath, int $maxEdge, int $quality): bool
    {
        $dir = dirname($destPath);
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return false;
        }

        $info = @getimagesize($srcPath);
        if (! $info || empty($info[0]) || empty($info[1])) {
            return false;
        }

        [$width, $height] = $info;
        $type = $info[2] ?? null;

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($srcPath),
            IMAGETYPE_PNG => @imagecreatefrompng($srcPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($srcPath) : false,
            IMAGETYPE_GIF => @imagecreatefromgif($srcPath),
            default => false,
        };

        if (! $source) {
            return false;
        }

        $scale = min(1, $maxEdge / max($width, $height));
        $newW = max(1, (int) round($width * $scale));
        $newH = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($newW, $newH);
        if (! $canvas) {
            imagedestroy($source);

            return false;
        }

        // خلفية بيضاء عشان الشفافية في PNG/GIF ما تتحولش لأسود
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, 0, 0, $newW, $newH, $white);

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
        imagedestroy($source);

        $ok = imagejpeg($canvas, $destPath, $quality);
        imagedestroy($canvas);

        return (bool) $ok && is_file($destPath);
    }
}
