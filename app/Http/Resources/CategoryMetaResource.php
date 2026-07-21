<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** Только то, что нужно generateMetadata на фронте: заголовок и meta-описание. */
final class CategoryMetaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'meta_description' => (string) $this->meta_description,
        ];
    }
}
