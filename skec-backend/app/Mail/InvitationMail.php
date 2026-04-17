<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $registrationLink;
    public string $appName;

    public function __construct(public Invitation $invitation)
    {
        $this->appName          = Setting::get('app_name', config('app.name'));
        $this->registrationLink = env('FRONTEND_URL') . '/register?token=' . $invitation->token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to {$this->appName} Learning Platform",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }
}
