<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class EmailOrPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || trim($value) === '') {
            return;
        }

        $trimmed = trim($value);

        // 1. Cek apakah format email valid
        if (filter_var($trimmed, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        // 2. Cek apakah format nomor telepon valid (HP / Telepon Indonesia atau internasional)
        // Karakter yang diizinkan: angka, +, spasi, tanda strip (-), tanda kurung ()
        $digitsOnly = preg_replace('/[^0-9]/', '', $trimmed);
        $isValidPhoneChars = preg_match('/^\+?[0-9\s\-()]+$/', $trimmed) === 1;

        if ($isValidPhoneChars && strlen($digitsOnly) >= 8 && strlen($digitsOnly) <= 16) {
            return;
        }

        $fail('Info kontak harus berupa alamat email yang valid atau nomor telepon aktif (contoh: 08123456789 atau kontak@email.com).');
    }
}
