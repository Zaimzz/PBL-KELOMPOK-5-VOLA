<?php

namespace App\Http\Requests\Eo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrganizerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->organizerProfile === null
            || $this->user()->organizerProfile->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', 'string', 'max:100'],
            'description'       => ['required', 'string', 'max:2000'],
            'city'              => ['required', 'string', 'max:100'],
            'pic_name'          => ['required', 'string', 'max:255'],
            'pic_phone'         => ['required', 'string', 'max:20'],
            'social_link'       => ['nullable', 'url', 'max:255'],
            'document'          => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120', // 5 MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'organization_name.required' => 'Nama organisasi wajib diisi.',
            'organization_name.max'      => 'Nama organisasi maksimal 255 karakter.',
            'organization_type.required' => 'Jenis organisasi wajib dipilih.',
            'organization_type.max'      => 'Jenis organisasi maksimal 100 karakter.',
            'description.required'       => 'Deskripsi organisasi wajib diisi.',
            'description.max'            => 'Deskripsi maksimal 2000 karakter.',
            'city.required'              => 'Kota/domisili wajib diisi.',
            'city.max'                   => 'Kota maksimal 100 karakter.',
            'pic_name.required'          => 'Nama PIC wajib diisi.',
            'pic_name.max'               => 'Nama PIC maksimal 255 karakter.',
            'pic_phone.required'         => 'Nomor telepon PIC wajib diisi.',
            'pic_phone.max'              => 'Nomor telepon maksimal 20 karakter.',
            'social_link.url'            => 'Social link harus berupa URL yang valid (contoh: https://instagram.com/nama).',
            'social_link.max'            => 'Social link maksimal 255 karakter.',
            'document.file'              => 'Dokumen harus berupa file.',
            'document.mimes'             => 'Dokumen harus berformat PDF, JPG, atau PNG.',
            'document.max'               => 'Ukuran dokumen maksimal 5 MB.',
        ];
    }
}
