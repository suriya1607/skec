<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:255', 'in:admission,academics,fees,facilities,general,other'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'Name is required.',
            'email.required'    => 'Email address is required.',
            'email.email'       => 'Please enter a valid email address.',
            'subject.required'  => 'Please select a subject.',
            'subject.in'        => 'Invalid subject selected.',
            'message.required'  => 'Message is required.',
            'message.min'       => 'Message must be at least 10 characters long.',
            'message.max'       => 'Message cannot exceed 5000 characters.',
        ];
    }
}
