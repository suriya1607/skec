<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $appName;
    public array $contactData;

    public function __construct(array $data)
    {
        $this->appName = Setting::where('key', 'app_name')->value('value') ?? 'SKEC';
        $this->contactData = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Contact Form Submission from {$this->contactData['name']} - {$this->contactData['subject']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
            with: [
                'contactData' => $this->contactData,
                'appName' => $this->appName,
            ]
        );
    }
}
