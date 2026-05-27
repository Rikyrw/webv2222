<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;

class NasabahProfilController extends Controller
{
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
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_]+$/'],
            'nama_nasabah' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ], [
            'username.required' => 'Username harus diisi',
            'username.min' => 'Username minimal 3 karakter',
            'username.max' => 'Username maksimal 50 karakter',
            'username.regex' => 'Username hanya boleh berisi huruf, angka, dan underscore',
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

            $exists = Nasabah::where('user_name', $username)
                ->where('id_nasabah', '!=', $id)
                ->exists();
            if ($exists) {
                return back()->withInput()->withErrors(['username' => 'Username sudah digunakan oleh pengguna lain']);
            }

            $payload = [
                'user_name' => $username,
                'nama_lengkap' => $validated['nama_nasabah'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'] ?? '',
                'alamat' => $validated['alamat'] ?? '',
            ];

            $model = Nasabah::findOrFail($id);
            $model->update($payload);
            $row = $model->fresh()->getAttributes();
                // Update session with new values
                session([
                    'nama_nasabah' => $row['nama_lengkap'] ?? $validated['nama_nasabah'],
                    'username' => $row['user_name'] ?? $username,
                    'email' => $row['email'] ?? $validated['email'],
                    'no_hp' => $row['no_hp'] ?? ($validated['no_hp'] ?? ''),
                    'alamat' => $row['alamat'] ?? ($validated['alamat'] ?? ''),
                ]);

                return redirect()->route('nasabah.profil')->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            \Log::error('Profile update error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
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
