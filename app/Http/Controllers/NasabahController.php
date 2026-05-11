<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nasabah;

class NasabahController extends Controller
{
    public function daftar(Request $request)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Daftar Nasabah';
        $flash = '';
        $nasabahs = [];

        // Handle POST action: approve or reject nasabah
        if ($request->isMethod('post') && $request->filled('action') && $request->filled('id_nasabah')) {
            $id = (int) $request->input('id_nasabah');
            $action = $request->input('action'); // expected: aktifkan | tolak

            // Validate CSRF token (use Laravel's default `_token` name)
            if (!hash_equals(session('_token', ''), $request->input('_token', ''))) {
                $flash = 'Token keamanan tidak valid.';
            } else {
                if ($action === 'aktifkan') {
                    $newStatus = 'aktif';
                    // Update nasabah status in database
                    Nasabah::where('id_nasabah', $id)->update(['status' => $newStatus]);
                    $flash = 'Status nasabah berhasil diperbarui.';
                } elseif ($action === 'tolak') {
                    $newStatus = 'nonaktif';
                    Nasabah::where('id_nasabah', $id)->update(['status' => $newStatus]);
                    $flash = 'Status nasabah berhasil diperbarui.';
                } elseif ($action === 'delete') {
                    // Permanently delete nasabah record
                    Nasabah::where('id_nasabah', $id)->delete();
                    $flash = 'Nasabah berhasil dihapus.';
                } else {
                    $flash = 'Aksi tidak dikenali.';
                }
            }
        }

        // Check session flash
        if (session()->has('flash_nasabah')) {
            $flash = session('flash_nasabah');
            session()->forget('flash_nasabah');
        }

        // Get nasabah data from database
        $nasabahs = Nasabah::select(
            'id_nasabah',
            'nama_lengkap as nama_nasabah',
            'alamat',
            'no_hp',
            'saldo',
            'status as status_akun',
            'created_at as tanggal_daftar'
        )
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'id_nasabah' => $item->id_nasabah,
                'nama_nasabah' => $item->nama_nasabah,
                'alamat' => $item->alamat ?? '-',
                'no_hp' => $item->no_hp ?? '-',
                'saldo' => $item->saldo ?? 0,
                'status_akun' => $item->status_akun ?? 'verifikasi',
                'tanggal_daftar' => $item->tanggal_daftar instanceof \DateTime ? $item->tanggal_daftar->format('Y-m-d') : (is_string($item->tanggal_daftar) ? $item->tanggal_daftar : '-'),
            ];
        })
        ->toArray();

        return view('admin.daftar_nasabah', compact(
            'activePage',
            'pageTitle',
            'flash',
            'nasabahs'
        ));
    }
}
