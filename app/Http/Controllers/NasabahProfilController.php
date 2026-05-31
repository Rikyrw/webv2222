<?php

namespace App\Http\Controllers;

use App\Mail\NasabahVerificationLinkMail;
use App\Models\Nasabah;
use App\Support\UsernamePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class NasabahProfilController extends Controller
{
    private const VERIFICATION_LINK_TTL_MINUTES = 60;

    public function index(Request $request)
    {
        $activePage = 'profil';

        $id = session('id_nasabah');
        $user = null;
        $initials = '';

        if (!$id) {
            return redirect()->route('nasabah.login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $model = Nasabah::find($id);
            if ($model) {
                $row = $model->getAttributes();
                $user = [
                    'id_nasabah' => $row['id_nasabah'] ?? null,
                    'nama_nasabah' => $row['nama_lengkap'] ?? ($row['nama_nasabah'] ?? ''),
                    'username' => $row['user_name'] ?? ($row['username'] ?? ''),
                    'email' => $row['email'] ?? '',
                    'alamat' => $row['alamat'] ?? '',
                    'no_hp' => $row['no_hp'] ?? '',
                    'saldo' => isset($row['saldo']) ? (float)$row['saldo'] : 0,
                    'tanggal_daftar' => $row['created_at'] ?? null,
                ];

                session([
                    'nama_nasabah' => $user['nama_nasabah'],
                    'saldo' => $user['saldo'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'alamat' => $user['alamat'],
                    'no_hp' => $user['no_hp'],
                ]);

                $initials = $this->getInitials($user['nama_nasabah'] ?? 'User');
            }
        } catch (\Exception $e) {
            \Log::error('Profile fetch error: ' . $e->getMessage());
        }

        // Fallback to session or defaults if the database fetch failed.
        if (!$user) {
            $user = [
                'id_nasabah' => session('id_nasabah') ?? null,
                'nama_nasabah' => session('nama_nasabah') ?? 'User',
                'username' => session('username') ?? '',
                'email' => session('email') ?? '',
                'alamat' => session('alamat') ?? '',
                'no_hp' => session('no_hp') ?? '',
                'saldo' => session('saldo') ?? 0,
                'tanggal_daftar' => null,
            ];
            $initials = $this->getInitials($user['nama_nasabah'] ?? 'User');
        }

        return view('nasabah.profil', compact('activePage', 'user', 'initials'));
    }
    public function edit(Request $request)
    {
        $activePage = 'profil';

        $id = session('id_nasabah');
        $user = [];

        if (!$id) {
            return redirect()->route('nasabah.login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $model = Nasabah::find($id);
            if ($model) {
                $row = $model->getAttributes();
                $user = [
                    'id_nasabah' => $row['id_nasabah'] ?? null,
                    'nama_nasabah' => $row['nama_lengkap'] ?? ($row['nama_nasabah'] ?? ''),
                    'username' => $row['user_name'] ?? ($row['username'] ?? ''),
                    'email' => $row['email'] ?? '',
                    'alamat' => $row['alamat'] ?? '',
                    'no_hp' => $row['no_hp'] ?? '',
                    'saldo' => isset($row['saldo']) ? (float)$row['saldo'] : 0,
                    'tanggal_daftar' => $row['created_at'] ?? ($row['tanggal_daftar'] ?? null),
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Profile edit fetch error: ' . $e->getMessage());
        }

        return view('nasabah.ubah-profil', compact('activePage', 'user'));
    }

    public function update(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'username' => UsernamePolicy::rules(required: true, min: 3),
            'nama_nasabah' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ], [
            'username.required' => 'Username harus diisi',
            'username.min' => 'Username minimal 3 karakter',
            'username.max' => 'Username maksimal 50 karakter',
            'username.regex' => UsernamePolicy::MESSAGE,
            'nama_nasabah.required' => 'Nama lengkap harus diisi',
            'nama_nasabah.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email tidak valid',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter',
            'no_hp.max' => 'Nomor handphone tidak boleh lebih dari 20 karakter',
            'alamat.max' => 'Alamat tidak boleh lebih dari 500 karakter',
        ]);

        $id = session('id_nasabah');
        if (!$id) {
            return redirect()->route('nasabah.login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            $username = trim($validated['username']);
            $newEmail = strtolower(trim($validated['email']));

            $exists = Nasabah::whereUsernameInsensitive($username)
                ->where('id_nasabah', '!=', $id)
                ->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['username' => 'Username sudah digunakan oleh pengguna lain']);
            }

            $emailExists = Nasabah::whereEmailInsensitive($newEmail)
                ->where('id_nasabah', '!=', $id)
                ->exists();
            if ($emailExists) {
                return back()->withInput()->withErrors(['email' => 'Email sudah digunakan oleh pengguna lain']);
            }

            $model = Nasabah::findOrFail($id);
            $currentEmail = strtolower(trim((string) $model->email));
            $emailChanged = $newEmail !== $currentEmail;
            $token = null;
            $expiresAt = null;

            $payload = [
                'user_name' => $username,
                'nama_lengkap' => $validated['nama_nasabah'],
                'email' => $newEmail,
                'no_hp' => $validated['no_hp'] ?? '',
                'alamat' => $validated['alamat'] ?? '',
            ];

            if ($emailChanged) {
                $token = $this->generateVerificationToken();
                $expiresAt = now()->addMinutes(self::VERIFICATION_LINK_TTL_MINUTES);
                $payload['email_verified_at'] = null;
                $payload['email_verification_token_hash'] = $this->tokenHash($token);
                $payload['email_verification_expires_at'] = $expiresAt;
                $payload['email_verification_sent_at'] = now();
            }

            $model->update($payload);
            $fresh = $model->fresh();
            $row = $fresh->getAttributes();

            if ($emailChanged && $token !== null && $expiresAt instanceof Carbon) {
                $verificationEmailSent = false;

                try {
                    $this->sendVerificationLink($row, $token, $expiresAt);
                    $verificationEmailSent = true;
                } catch (\Throwable $exception) {
                    Log::warning('Profile email verification link failed.', [
                        'id_nasabah' => $id,
                        'email' => $newEmail,
                        'message' => $exception->getMessage(),
                    ]);
                }

                $request->session()->forget([
                    'id_nasabah',
                    'nama_nasabah',
                    'username',
                    'email',
                    'alamat',
                    'no_hp',
                    'saldo',
                ]);
                $request->session()->put(NasabahEmailVerificationController::SESSION_KEY, [
                    'id_nasabah' => (int) $row['id_nasabah'],
                    'email' => $newEmail,
                ]);

                $redirect = redirect()->route('nasabah.verification.notice');

                return $verificationEmailSent
                    ? $redirect->with('success', 'Email profil diperbarui. Link verifikasi sudah dikirim ke email baru. Silakan verifikasi sebelum login lagi.')
                    : $redirect->with('error', 'Email profil diperbarui, tetapi link verifikasi belum dapat dikirim. Gunakan tombol kirim ulang verifikasi.');
            }

            // Update session with new values.
            session([
                'nama_nasabah' => $row['nama_lengkap'] ?? $validated['nama_nasabah'],
                'username' => $row['user_name'] ?? $username,
                'email' => $row['email'] ?? $newEmail,
                'no_hp' => $row['no_hp'] ?? ($validated['no_hp'] ?? ''),
                'alamat' => $row['alamat'] ?? ($validated['alamat'] ?? ''),
            ]);

            return redirect()->route('nasabah.profil')->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    private function sendVerificationLink(array $user, string $token, Carbon $expiresAt): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'nasabah.verification.verify',
            $expiresAt,
            [
                'id' => (int) $user['id_nasabah'],
                'token' => $token,
            ],
        );

        Mail::to($user['email'])->send(new NasabahVerificationLinkMail(
            $user['nama_lengkap'] ?? 'Nasabah',
            $verificationUrl,
            self::VERIFICATION_LINK_TTL_MINUTES,
        ));
    }

    private function generateVerificationToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function tokenHash(string $token): string
    {
        return hash('sha256', $token);
    }

    private function getInitials($name)
    {
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            if (trim($word) !== '') {
                $initials .= strtoupper(substr($word, 0, 1));
                if (strlen($initials) >= 2) break;
            }
        }
        // Jika hanya 1 huruf, tambahkan huruf pertama lagi
        if (strlen($initials) == 1 && isset($words[0])) {
            $initials .= strtoupper(substr($words[0], 1, 1));
        }
        return $initials;
    }
}
