<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesUploadUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Только то, что нужно generateMetadata на фронте: заголовок, meta-описание, картинка под og:image. */
final class ProductMetaResource extends JsonResource
{
    use ResolvesUploadUrl;

    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'meta_description' => (string) $this->meta_description,
            // og:image — первая картинка галереи
            'image_url' => self::uploadUrls($this->images)[0] ?? null,
            'category_slug' => $this->whenLoaded('category', fn () => $this->category?->slug),
        ];
    }
}
