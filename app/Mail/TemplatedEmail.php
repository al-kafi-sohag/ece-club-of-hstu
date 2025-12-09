<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $content;
    public $bladeTemplate;
    public $variables;

    public function __construct($subject, $content, $bladeTemplate, $variables)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->bladeTemplate = $bladeTemplate;
        $this->variables = $variables;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->bladeTemplate,
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
