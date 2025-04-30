<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExportReady extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected string $filename)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your task export is ready'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.export-ready',
            with: [
                'filename' => $this->filename,
                'downloadUrl' => route('tasks.export.download', ['filename' => $this->filename]),
            ],
        );
    }
} 