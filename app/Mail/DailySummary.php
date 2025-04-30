<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Services\LanguageService;

class DailySummary extends Mailable
{
    use Queueable, SerializesModels;

    public $stats;
    public $todayTasks;
    public $overdueTasks;
    public $completedTasks;
    public $language;

    public function __construct($stats, $todayTasks, $overdueTasks, $completedTasks)
    {
        $this->stats = $stats;
        $this->todayTasks = $todayTasks;
        $this->overdueTasks = $overdueTasks;
        $this->completedTasks = $completedTasks;
        $this->language = LanguageService::getCurrentLanguage();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Task Summary - ' . now()->format('Y-m-d'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-summary',
            with: [
                'stats' => $this->stats,
                'todayTasks' => $this->todayTasks,
                'overdueTasks' => $this->overdueTasks,
                'completedTasks' => $this->completedTasks,
                'language' => $this->language,
            ],
        );
    }
} 