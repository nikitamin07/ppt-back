<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

final class SiteVisitController extends Controller
{
    /**
     * Пинг с фронта на каждый заход на сайт (см. shared/lib/track-visit в ppt-front).
     * Атомарный upsert по дню — гонки параллельных запросов не теряют инкремент.
     */
    public function store(): Response
    {
        $now = now();

        DB::statement(
            'insert into site_visits (date, count, created_at, updated_at)
             values (?, 1, ?, ?)
             on conflict (date) do update set count = site_visits.count + 1, updated_at = excluded.updated_at',
            [$now->toDateString(), $now, $now],
        );

        return response()->noContent();
    }
}
