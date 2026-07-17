<?php

declare(strict_types=1);

namespace App\Http\Resources\Concerns;

trait ResolvesUploadUrl
{
    /** Загрузки админки лежат на диске public, наружу отдаются через /storage. */
    private static function uploadUrl(?string $path): ?string
    {
        return $path !== null ? '/storage/'.ltrim($path, '/') : null;
    }
}
