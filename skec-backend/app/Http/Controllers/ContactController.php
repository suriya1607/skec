<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Mail\ContactMail;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Models\Setting;

class ContactController extends Controller
{
    use ApiResponse;

    /**
     * Send a contact message
     */
    public function sendMessage(ContactRequest $request): JsonResponse
    {
        try {
            // Get admin email(s) from settings or use default
            $adminEmail = Setting::where('key', 'admin_email')->value('value') ?? 'info@skecinstitute.in';

            // Send email to admin
            Mail::to($adminEmail)->send(new ContactMail($request->validated()));

            return $this->success(
                null,
                'Your message has been sent successfully. We will get back to you soon.',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            return $this->error(
                'Failed to send message. Please try again later.',
                'contact_send_failed',
                500
            );
        }
    }
}
