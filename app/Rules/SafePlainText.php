<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafePlainText implements ValidationRule
{
    public function __construct(private bool $multiline = false) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(':attribute hanya boleh berisi teks.');

            return;
        }

        if (preg_match('/[<>]/u', $value)
            || preg_match('/javascript\s*:|data\s*:|vbscript\s*:/i', $value)
            || preg_match('/<\?|\{[{!#]|on\w+\s*=/i', $value)
        ) {
            $fail(':attribute hanya boleh teks biasa, tanpa kode atau skrip.');

            return;
        }

        $pattern = $this->multiline
            ? '/^[\p{L}\p{M}\p{N}\p{Zs}\p{P}\p{S}\n\t]+$/u'
            : '/^[\p{L}\p{M}\p{N}\p{Zs}\p{P}\p{S}]+$/u';

        if (! preg_match($pattern, $value)) {
            $fail(':attribute hanya boleh berisi teks.');
        }
    }
}
