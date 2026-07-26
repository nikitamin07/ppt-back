<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class ImageOptimizer
{
    /**
     * Ужать загруженное изображение в WebP: кап по большей стороне, качество ~80, EXIF срезается.
     * orient() докручивает поворот по EXIF до срезки метаданных. Возвращает путь на диске public.
     */
    public static function storeWebp(UploadedFile $file, string $directory, int $maxDimension, int $quality = 80): string
    {
        $image = (new ImageManager(Driver::class))->decodePath($file->getRealPath());
        $image->orient()->scaleDown($maxDimension, $maxDimension);

        $path = trim($directory, '/').'/'.Str::ulid()->toBase32().'.webp';
        Storage::disk('public')->put($path, (string) $image->encode(new WebpEncoder(quality: $quality, strip: true)));

        return $path;
    }
}
