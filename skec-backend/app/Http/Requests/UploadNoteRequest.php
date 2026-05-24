<?php

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UploadNoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $maxSizeMb = Setting::get('max_file_size_mb', 50);
        $maxSizeKb = $maxSizeMb * 1024;

        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'category_id'    => ['nullable', 'string'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['exists:note_categories,id'],
            'subject_id'     => ['nullable', 'exists:note_subjects,id'],
            'file'           => ['required', 'file', 'mimes:pdf', "max:{$maxSizeKb}"],
            'status'         => ['nullable', 'in:published,draft'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'Only PDF files are allowed.',
            'file.max'   => 'File size exceeds the maximum allowed limit.',
        ];
    }
}
