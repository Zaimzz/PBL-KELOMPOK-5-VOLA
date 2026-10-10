<?php

namespace App\Http\Requests\Eo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware and policy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:event_categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'registration_deadline' => ['nullable', 'date'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'coordination_link' => ['nullable', 'url', 'max:500'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'pic_phone' => ['nullable', 'string', 'max:20'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['string', 'max:100'],
            'poster' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB
            ],
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
            'title.required' => 'Nama event wajib diisi.',
            'title.max' => 'Nama event tidak boleh lebih dari 255 karakter.',
            'category_id.required' => 'Kategori event wajib dipilih.',
            'category_id.exists' => 'Kategori event tidak valid.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 5000 karakter.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'start_time.date_format' => 'Format waktu mulai tidak valid (gunakan HH:MM).',
            'end_time.date_format' => 'Format waktu selesai tidak valid (gunakan HH:MM).',
            'registration_deadline.date' => 'Format tanggal pendaftaran tidak valid.',
            'coordination_link.url' => 'Link koordinasi harus berupa URL yang valid.',
            'poster.mimes' => 'Poster harus berupa file JPG, PNG, atau WEBP.',
            'poster.max' => 'Ukuran poster tidak boleh lebih dari 2 MB.',
        ];
    }
}
