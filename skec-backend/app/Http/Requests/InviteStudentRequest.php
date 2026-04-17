<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InviteStudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'unique:users,email', 'unique:invitations,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'An invitation or account for this email already exists.',
        ];
    }
}
