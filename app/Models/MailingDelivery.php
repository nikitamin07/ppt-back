<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MailingDelivery extends Model
{
    protected $fillable = ['mailing_id', 'email', 'recipient_id', 'status', 'error', 'sent_at', 'provider_message_id'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function mailing(): BelongsTo
    {
        return $this->belongsTo(Mailing::class);
    }
}
