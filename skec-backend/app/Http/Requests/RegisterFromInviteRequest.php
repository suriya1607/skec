<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterFromInviteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'token'               => ['required', 'string', 'size:64'],
            'name'                => ['required', 'string', 'min:2', 'max:255'],
            'password'            => ['required', 'string', 'min:8', 'confirmed'],
            'photo'               => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'reg_no'              => ['required', 'string', 'max:50', 'unique:student_profiles,reg_no'],
            'father_name'         => ['required', 'string', 'min:2', 'max:255'],
            'dob'                 => ['required', 'date', 'before:today'],
            'gender'              => ['required', 'in:male,female,other'],
            'address'             => ['required', 'string', 'min:5', 'max:1000'],
            'community_category'  => ['required', 'in:MBC,OBC,SC,ST,BCM,EWS,EBC'],
            'contact_phone'       => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'qualification'       => ['required', 'string', 'max:255'],
            'course_id'           => ['required', 'exists:note_categories,id'],
            'medium_of_studying'  => ['required', 'in:english,tamil'],
        ];
    }
}
