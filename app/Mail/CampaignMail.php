<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Письмо рассылки, шлётся синхронно — не ShouldQueue */
final class CampaignMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * @param  string[]  $attachmentPaths  пути на диске 'local'
     */
    public function __construct(
        public string $subjectLine,
        public string $bodyHtml,
        public array $attachmentPaths = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.campaign',
            text: 'emails.campaign_text',
            with: [
                'body' => $this->bodyHtml,
                'textBody' => $this->toPlainText($this->bodyHtml),
            ],
        );
    }

    /** Грубая HTML→текст конвертация для plain-text альтернативы письма */
    private function toPlainText(string $html): string
    {
        $normalized = preg_replace('#<(br|/p|/div|/li)\s*/?>#i', "\n", $html);

        return trim(html_entity_decode(strip_tags($normalized), ENT_QUOTES, 'UTF-8'));
    }

    /**
     * @return Attachment[]
     */
    public function attachments(): array
    {
        return array_map(
            fn (string $path): Attachment => Attachment::fromStorageDisk('local', $path),
            $this->attachmentPaths,
        );
    }
}
