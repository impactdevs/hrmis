<?php

namespace App\Mail;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly JobApplication $application,
        public readonly string $previousStatus,
    ) {}

    public function envelope(): Envelope
    {
        $ref = $this->application->reference_number;

        $subject = match ($this->application->status) {
            'shortlisted' => "You Have Been Shortlisted — {$ref}",
            'interviewed' => "Interview Update — {$ref}",
            'offered'     => "Job Offer — {$ref}",
            'hired'       => "Welcome Aboard — {$ref}",
            'rejected'    => "Application Outcome — {$ref}",
            default       => "Application Update — {$ref}",
        };

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.application-status-changed');
    }
}