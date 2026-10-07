<?php

namespace App\Http\Requests\Eo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventPositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'requirements' => ['nullable', 'string', 'max:1000'],
            'quota' => ['required', 'integer', 'min:1', 'max:9999'],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama posisi wajib diisi.',
            'name.max' => 'Nama posisi tidak boleh lebih dari 255 karakter.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
            'requirements.max' => 'Persyaratan tidak boleh lebih dari 1000 karakter.',
            'quota.required' => 'Kuota wajib diisi.',
            'quota.integer' => 'Kuota harus berupa angka.',
            'quota.min' => 'Kuota minimal 1 orang.',
            'quota.max' => 'Kuota tidak boleh lebih dari 9999 orang.',
        ];
    }
}
