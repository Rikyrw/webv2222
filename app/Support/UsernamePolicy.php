<?php

namespace App\Support;

class UsernamePolicy
{
    public const MESSAGE = 'Username hanya boleh berisi huruf, angka, dan underscore tanpa spasi.';

    public static function rules(bool $required = true, int $min = 0, int $max = 50): array
    {
        $rules = [$required ? 'required' : 'nullable', 'string'];

        if ($min > 0) {
            $rules[] = 'min:'.$min;
        }

        $rules[] = 'max:'.$max;
        $rules[] = 'regex:/^[A-Za-z0-9_]+$/';

        return $rules;
    }

    public static function messages(string $field): array
    {
        return [
            $field.'.regex' => self::MESSAGE,
        ];
    }

    public static function fromEmail(string $email): string
    {
        $base = preg_replace('/[^A-Za-z0-9_]/', '', explode('@', $email)[0]) ?? '';

        return $base !== '' ? $base : 'nasabah';
    }
}
