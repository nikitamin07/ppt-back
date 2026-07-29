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
            with: ['body' => $this->bodyHtml],
        );
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
