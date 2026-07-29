<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\CampaignMail;
use App\Models\Mailing;
use App\Models\MailingDelivery;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class DispatchMailings extends Command
{
    protected $signature = 'mailing:dispatch {--force : Игнорировать окно отправки}';

    protected $description = 'Отправляет очередное письмо активных рассылок';

    public function handle(): int
    {
        $config = config('mailing');

        if (! $this->option('force') && ! $this->withinWindow($config)) {
            return self::SUCCESS;
        }

        if ($this->tooSoon($config) || $this->dailyLimitReached($config)) {
            return self::SUCCESS;
        }

        // Старую рассылку добиваем раньше новой
        $delivery = MailingDelivery::query()
            ->where('status', 'pending')
            ->whereHas('mailing', fn (Builder $q) => $q->where('status', 'sending'))
            ->with('mailing')
            ->orderBy('mailing_id')
            ->orderBy('id')
            ->first();

        if ($delivery) {
            $this->sendOne($delivery);
        }

        $this->finishCompleted();

        return self::SUCCESS;
    }

    /** @param array<string, mixed> $config */
    private function tooSoon(array $config): bool
    {
        $interval = (int) $config['interval_seconds'];
        $last = MailingDelivery::max('sent_at');

        return $interval > 0
            && $last !== null
            && CarbonImmutable::parse($last)->addSeconds($interval)->isFuture();
    }

    /** @param array<string, mixed> $config */
    private function dailyLimitReached(array $config): bool
    {
        $limit = (int) $config['daily_limit'];
        if ($limit <= 0) {
            return false;
        }

        $since = now($config['timezone'])->startOfDay()->utc();

        return MailingDelivery::whereNotNull('sent_at')->where('sent_at', '>=', $since)->count() >= $limit;
    }

    private function sendOne(MailingDelivery $delivery): void
    {
        $mailing = $delivery->mailing;
        if (! $mailing) {
            return;
        }

        if ($mailing->started_at === null) {
            $mailing->update(['started_at' => now(), 'status' => 'sending']);
        }

        try {
            $sent = Mail::to($delivery->email)->send(
                new CampaignMail($mailing->subject, $mailing->body, $mailing->attachments ?? []),
            );
            // Message-ID — для сверки с webhook
            $delivery->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider_message_id' => $sent?->getMessageId(),
            ]);
            $mailing->increment('sent_count');
        } catch (Throwable $e) {
            $delivery->update(['status' => 'failed', 'error' => mb_substr($e->getMessage(), 0, 1000)]);
            $mailing->increment('failed_count');
            report($e);
        }
    }

    private function finishCompleted(): void
    {
        Mailing::query()
            ->where('status', 'sending')
            ->whereDoesntHave('deliveries', fn (Builder $q) => $q->where('status', 'pending'))
            ->update(['status' => 'sent', 'finished_at' => now()]);
    }

    /** @param array<string, mixed> $config */
    private function withinWindow(array $config): bool
    {
        $hour = now($config['timezone'])->hour;

        return $hour >= (int) $config['window']['start']
            && $hour < (int) $config['window']['end'];
    }
}
