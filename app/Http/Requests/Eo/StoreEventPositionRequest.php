<?php

namespace App\Http\Requests\Eo;

use App\Models\Event;
use App\Models\EventPosition;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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
     * Tolak nama posisi yang sama dengan posisi lain pada event yang sama.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $event = $this->route('event');
                $position = $this->route('position');
                $name = mb_strtolower(trim((string) $this->input('name')));

                if (! $event instanceof Event || $name === '') {
                    return;
                }

                $duplicate = $event->positions()
                    ->when($position instanceof EventPosition, fn ($query) => $query->where('id', '!=', $position->id))
                    ->whereRaw('LOWER(name) = ?', [$name])
                    ->exists();

                if ($duplicate) {
                    $validator->errors()->add('name', 'Posisi dengan nama tersebut sudah ada pada event ini.');
                }
            },
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
