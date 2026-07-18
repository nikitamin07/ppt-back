<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Одна строка на день; count наращивается через SiteVisitController::store(), не через save(). */
final class SiteVisit extends Model
{
    protected $fillable = ['date', 'count'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }
}
