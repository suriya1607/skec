<?php

namespace App\Mail;

use App\Models\Note;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NoteMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $appName;
    public string $noteUrl;

    public function __construct(public Note $note)
    {
        $this->appName = Setting::get('app_name', config('app.name'));
        $this->noteUrl = env('FRONTEND_URL') . '/notes/' . $note->slug;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Note: {$this->note->title} uploaded to {$this->appName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.note',
        );
    }
}
