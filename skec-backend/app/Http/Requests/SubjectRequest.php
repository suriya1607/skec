<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', "unique:note_subjects,slug,{$id}"],
            'description' => ['nullable', 'string'],
            'color'       => ['nullable', 'string', 'max:20'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'sort_order'  => ['nullable', 'integer'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }
}
