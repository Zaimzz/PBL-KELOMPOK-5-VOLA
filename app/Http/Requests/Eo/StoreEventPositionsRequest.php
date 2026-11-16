<?php

namespace App\Http\Requests\Eo;

use App\Models\Event;
use App\Models\PositionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreEventPositionsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event && Gate::allows('managePositions', $event);
    }

    /**
     * Rapikan input. Untuk posisi katalog, nama dan kategori selalu diambil dari
     * data katalog (bukan dari input pengguna).
     */
    protected function prepareForValidation(): void
    {
        $input = $this->input('positions');

        if (! is_array($input)) {
            return;
        }

        $typeIds = collect($input)
            ->map(fn ($position) => is_array($position) ? ($position['position_type_id'] ?? null) : null)
            ->filter()
            ->unique()
            ->values();

        $types = PositionType::active()->whereIn('id', $typeIds)->get()->keyBy('id');

        $normalized = collect($input)->map(function ($position) use ($types): array {
            $position = is_array($position) ? $position : [];
            $type = $types->get($position['position_type_id'] ?? null);

            if ($type) {
                $position['name'] = $type->name;
                $position['category'] = $type->category;
            } else {
                $position['name'] = trim((string) preg_replace('/\s+/', ' ', (string) ($position['name'] ?? '')));
                $position['category'] = trim((string) ($position['category'] ?? '')) ?: null;
            }

            return $position;
        });

        $this->merge(['positions' => $normalized->values()->all()]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'positions' => ['required', 'array', 'min:1', 'max:30'],
            'positions.*.position_type_id' => [
                'nullable',
                'integer',
                Rule::exists('position_types', 'id')->where('is_active', true),
            ],
            'positions.*.name' => ['required', 'string', 'max:255'],
            'positions.*.category' => ['nullable', 'string', 'max:100'],
            'positions.*.quota' => ['required', 'integer', 'min:1', 'max:9999'],
            'positions.*.description' => ['nullable', 'string', 'max:1000'],
            'positions.*.requirements' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Tolak nama posisi yang sudah ada di event atau dipilih dua kali dalam satu permintaan.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $event = $this->route('event');

                $taken = $event->positions()
                    ->pluck('name')
                    ->map(fn (string $name): string => mb_strtolower(trim($name)))
                    ->all();

                foreach ((array) $this->input('positions', []) as $index => $position) {
                    $key = mb_strtolower(trim((string) ($position['name'] ?? '')));

                    if ($key === '') {
                        continue;
                    }

                    if (in_array($key, $taken, true)) {
                        $validator->errors()->add(
                            "positions.{$index}.name",
                            "Posisi \"{$position['name']}\" sudah ada pada event ini atau dipilih lebih dari sekali."
                        );

                        continue;
                    }

                    $taken[] = $key;
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
            'positions.required' => 'Pilih atau tambahkan minimal 1 posisi.',
            'positions.min' => 'Pilih atau tambahkan minimal 1 posisi.',
            'positions.max' => 'Maksimal 30 posisi dapat ditambahkan sekaligus.',
            'positions.*.position_type_id.exists' => 'Posisi katalog yang dipilih tidak valid.',
            'positions.*.name.required' => 'Nama posisi wajib diisi.',
            'positions.*.name.max' => 'Nama posisi tidak boleh lebih dari 255 karakter.',
            'positions.*.quota.required' => 'Kuota wajib diisi.',
            'positions.*.quota.integer' => 'Kuota harus berupa angka bulat.',
            'positions.*.quota.min' => 'Kuota minimal 1 orang.',
            'positions.*.quota.max' => 'Kuota tidak boleh lebih dari 9999 orang.',
            'positions.*.description.max' => 'Deskripsi tidak boleh lebih dari 1000 karakter.',
            'positions.*.requirements.max' => 'Kualifikasi tidak boleh lebih dari 1000 karakter.',
        ];
    }
}
