<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
{
    return [
        'name' => ['sometimes', 'string', 'min:2', 'max:255'],

        'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

        'reg_no' => [
            'sometimes',
            'string',
            'max:50',
            Rule::unique('student_profiles', 'reg_no')
                ->ignore($this->user()?->profile?->id),
        ],

        'father_name' => ['sometimes', 'string', 'min:2', 'max:255'],
        'dob' => ['sometimes', 'date', 'before:today'],
        'gender' => ['sometimes', 'in:male,female,other'],
        'address' => ['sometimes', 'string', 'min:5', 'max:1000'],

        'community_category' => [
            'sometimes',
            'in:MBC,OBC,SC,ST,BCM,EWS,EBC'
        ],

        'contact_phone' => [
            'sometimes',
            'string',
            'max:20',
            'regex:/^[0-9+\-\s()]{7,20}$/'
        ],

        'qualification' => ['sometimes', 'string', 'max:255'],

        'course_id' => [
            'sometimes',
            'nullable',
            'string',
        ],

        'medium_of_studying' => [
            'sometimes',
            'in:english,tamil'
        ],

        'password' => [
            'nullable',
            'string',
            'min:8',
            'confirmed'
        ],

        'password_confirmation' => ['nullable', 'string'],
    ];
}
}
