<?php
// ═══════════════════════════════════════════════════════
// SAVE AS: app/Mail/ApplicationReceivedMail.php
// ═══════════════════════════════════════════════════════

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly JobApplication $application,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Application Received — ' . $this->application->reference_number,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-received');
    }
}