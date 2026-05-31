<?php

namespace App\Http\Middleware;

use App\Http\Controllers\NasabahEmailVerificationController;
use App\Models\Nasabah;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NasabahSession
{
    public function handle(Request $request, Closure $next): Response
    {
        $nasabahId = session('id_nasabah');

        if (! $nasabahId) {
            if (session()->has(NasabahEmailVerificationController::SESSION_KEY)) {
                return redirect()
                    ->route('nasabah.verification.notice')
                    ->with('error', 'Email belum diverifikasi.');
            }

            return redirect()
                ->route('nasabah.login')
                ->with('error', 'Silakan login terlebih dahulu');
        }

        $nasabah = Nasabah::find($nasabahId);

        if (! $nasabah || $nasabah->status !== 'aktif') {
            session()->forget([
                'id_nasabah',
                'nama_nasabah',
                'username',
                'email',
                'alamat',
                'no_hp',
                'saldo',
                'validated_waste_photos',
            ]);

            return redirect()
                ->route('nasabah.login')
                ->with('error', $nasabah
                    ? 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.'
                    : 'Sesi tidak valid. Silakan login kembali.');
        }

        return $next($request);
    }
}
