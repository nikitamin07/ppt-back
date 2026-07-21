<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

trait ResolvesUploadUrl
{
    /** Загрузки админки лежат на диске public, наружу отдаются через /storage. */
    protected static function uploadUrl(?string $path): ?string
    {
        return $path !== null ? '/storage/'.ltrim($path, '/') : null;
    }

    /**
     * Галерея: порядок массива сохраняется (это порядок, заданный в админке).
     *
     * @param  array<int, string|null>|null  $paths
     * @return list<string>
     */
    protected static function uploadUrls(?array $paths): array
    {
        return array_values(array_filter(array_map(
            static fn ($path) => is_string($path) ? self::uploadUrl($path) : null,
            $paths ?? [],
        )));
    }
}
