<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Mailing extends Model
{
    protected $fillable = [
        'subject', 'body', 'attachments', 'send_to_all', 'status',
        'total', 'sent_count', 'failed_count', 'started_at', 'finished_at',
        'paused_by_window', 'window_override',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'send_to_all' => 'boolean',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'paused_by_window' => 'boolean',
            'window_override' => 'boolean',
        ];
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(MailingDelivery::class);
    }

    /**
     * Снимок получателей в очередь; null — все активные.
     *
     * @param  int[]|null  $recipientIds
     */
    public function buildDeliveries(?array $recipientIds): void
    {
        $query = MailingRecipient::query()->active();
        if ($recipientIds !== null) {
            $query->whereIn('id', $recipientIds);
        }

        $now = now();
        $rows = $query->get(['id', 'email'])->map(fn (MailingRecipient $r): array => [
            'mailing_id' => $this->id,
            'email' => $r->email,
            'recipient_id' => $r->id,
            'status' => 'pending',
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        foreach (array_chunk($rows, 500) as $chunk) {
            MailingDelivery::insert($chunk);
        }

        $this->update([
            'total' => count($rows),
            'status' => $rows === [] ? 'sent' : 'sending',
        ]);
    }

    /** @return array<string, int> доставки по статусам */
    public function deliveryCounts(): array
    {
        $rows = $this->deliveries()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        return [
            'delivered' => (int) $rows->get('delivered', 0),
            'bounced' => (int) $rows->get('bounced', 0),
            'complained' => (int) $rows->get('complained', 0),
            'failed' => (int) $rows->get('failed', 0),
            'sent' => (int) $rows->get('sent', 0),
            'pending' => (int) $rows->get('pending', 0),
        ];
    }

    public static function isSendingWindowOpen(): bool
    {
        $hour = now(config('mailing.timezone'))->hour;
        $window = config('mailing.window');

        return $hour >= (int) $window['start'] && $hour < (int) $window['end'];
    }

    public static function syncWindowState(bool $windowOpen): void
    {
        if ($windowOpen) {
            static::query()
                ->where('status', 'paused')
                ->where('paused_by_window', true)
                ->update(['status' => 'sending', 'paused_by_window' => false]);

            static::query()->where('window_override', true)->update(['window_override' => false]);

            return;
        }

        static::query()
            ->where('status', 'sending')
            ->where('window_override', false)
            ->update(['status' => 'paused', 'paused_by_window' => true]);
    }

    public function pause(): void
    {
        if ($this->status === 'sending') {
            $this->update(['status' => 'paused', 'paused_by_window' => false, 'window_override' => false]);
        }
    }

    public function resume(): void
    {
        if ($this->status === 'paused') {
            $this->update([
                'status' => 'sending',
                'paused_by_window' => false,
                'window_override' => ! self::isSendingWindowOpen(),
            ]);
        }
    }

    public function requeueFailed(): int
    {
        $count = $this->deliveries()->where('status', 'failed')->update([
            'status' => 'pending',
            'error' => null,
            'sent_at' => null,
        ]);

        if ($count > 0) {
            $this->update([
                'failed_count' => max(0, $this->failed_count - $count),
                'status' => 'sending',
                'finished_at' => null,
            ]);
        }

        return $count;
    }
}
