<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Isbn implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return; // Let nullable handle empty values
        }

        // Remove hyphens and spaces
        $isbn = preg_replace('/[-\s]/', '', $value);

        // Check ISBN-10
        if (strlen($isbn) === 10) {
            if (!$this->isValidIsbn10($isbn)) {
                $fail('رقم ISBN-10 غير صالح');
            }
            return;
        }

        // Check ISBN-13
        if (strlen($isbn) === 13) {
            if (!$this->isValidIsbn13($isbn)) {
                $fail('رقم ISBN-13 غير صالح');
            }
            return;
        }

        $fail('يجب أن يكون رقم ISBN من 10 أو 13 رقماً');
    }

    private function isValidIsbn10(string $isbn): bool
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            if (!is_numeric($isbn[$i])) {
                return false;
            }
            $sum += (int) $isbn[$i] * (10 - $i);
        }

        $lastChar = strtoupper($isbn[9]);
        $check = ($lastChar === 'X') ? 10 : (int) $lastChar;

        return ($sum + $check) % 11 === 0;
    }

    private function isValidIsbn13(string $isbn): bool
    {
        if (!ctype_digit($isbn)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $weight = ($i % 2 === 0) ? 1 : 3;
            $sum += (int) $isbn[$i] * $weight;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;

        return (int) $isbn[12] === $checkDigit;
    }
}
