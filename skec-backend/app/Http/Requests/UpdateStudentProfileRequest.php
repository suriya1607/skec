<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'in:active,inactive'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'father_name' => ['nullable', 'string'],
            'dob' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string'],
            'community_category' => ['nullable', 'string'],
            'contact_phone' => ['nullable', 'string'],
            'qualification' => ['nullable', 'string'],
            'course_id' => ['nullable', 'string'],
            'course_ids' => ['nullable', 'array'],
            'course_ids.*' => ['integer'],
            'medium_of_studying' => ['nullable', 'in:english,tamil'],
        ];
    }
}
