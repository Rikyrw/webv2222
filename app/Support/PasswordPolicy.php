<?php

namespace App\Support;

use Closure;

class PasswordPolicy
{
    public const WARNING_SESSION_KEY = 'nasabah_password_change_warning';

    public const WARNING_MESSAGE = 'Kata sandi Anda belum memenuhi ketentuan terbaru. Silakan ganti password melalui fitur Lupa Password agar akun lebih aman.';

    public static function rules(): array
    {
        return ['required', 'string', self::rule()];
    }

    public static function confirmationRules(string $passwordField = 'password'): array
    {
        return ['required', 'string', 'same:'.$passwordField];
    }

    public static function messages(
        string $passwordField = 'password',
        string $confirmationField = 'konfirmasi_password'
    ): array {
        return [
            $passwordField.'.required' => 'Kata sandi harus diisi.',
            $passwordField.'.string' => 'Kata sandi harus berupa teks.',
            $confirmationField.'.required' => 'Konfirmasi kata sandi harus diisi.',
            $confirmationField.'.same' => 'Kata sandi dan konfirmasi kata sandi tidak sama.',
        ];
    }

    public static function warningFor(string $password): ?string
    {
        return self::isCompliant($password) ? null : self::WARNING_MESSAGE;
    }

    public static function isCompliant(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password) === 1
            && preg_match('/[a-z]/', $password) === 1
            && preg_match('/[0-9]/', $password) === 1
            && preg_match('/[!@#$%^&*]/', $password) === 1;
    }

    private static function rule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $password = (string) $value;

            if (strlen($password) < 8) {
                $fail('Kata sandi minimal 8 karakter.');
            }

            if (preg_match('/[A-Z]/', $password) !== 1) {
                $fail('Kata sandi harus mengandung huruf besar (A-Z).');
            }

            if (preg_match('/[a-z]/', $password) !== 1) {
                $fail('Kata sandi harus mengandung huruf kecil (a-z).');
            }

            if (preg_match('/[0-9]/', $password) !== 1) {
                $fail('Kata sandi harus mengandung angka (0-9).');
            }

            if (preg_match('/[!@#$%^&*]/', $password) !== 1) {
                $fail('Kata sandi harus mengandung karakter khusus (!@#$%^&*).');
            }
        };
    }
}
