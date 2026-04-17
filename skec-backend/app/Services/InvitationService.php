<?php

namespace App\Services;

use App\Exceptions\InvitationExpiredException;
use App\Exceptions\InvitationUsedException;
use App\Exceptions\InvitationNotFoundException;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InvitationService
{
    public function create(string $email, int $invitedBy): Invitation
    {
        $expiryHours = Setting::get('invite_expiry_hours', 48);

        return Invitation::create([
            'email'      => $email,
            'token'      => Str::random(64),
            'invited_by' => $invitedBy,
            'expires_at' => now()->addHours($expiryHours),
        ]);
    }

    public function validate(string $token): Invitation
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            throw new InvitationNotFoundException('Invitation not found.');
        }

        if ($invitation->isUsed()) {
            throw new InvitationUsedException('This invitation has already been used.');
        }

        if ($invitation->isExpired()) {
            throw new InvitationExpiredException('This invitation has expired.');
        }

        return $invitation;
    }

    public function markUsed(Invitation $invitation, User $user): void
    {
        $invitation->update([
            'used_at' => now(),
            'used_by' => $user->id,
        ]);
    }

    public function resend(Invitation $invitation): void
    {
        $expiryHours = Setting::get('invite_expiry_hours', 48);

        $invitation->update([
            'expires_at'   => now()->addHours($expiryHours),
            'resend_count' => $invitation->resend_count + 1,
        ]);

        Mail::to($invitation->email)->queue(new InvitationMail($invitation));
    }

    public function sendMail(Invitation $invitation): void
    {
        Mail::to($invitation->email)->queue(new InvitationMail($invitation));
    }
}
