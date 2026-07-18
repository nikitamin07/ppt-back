<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class CallbackRequest extends Model
{
    protected $fillable = ['name', 'phone', 'comment', 'source_url'];

    protected function casts(): array
    {
        return [
            'is_processed' => 'boolean',
        ];
    }
}
