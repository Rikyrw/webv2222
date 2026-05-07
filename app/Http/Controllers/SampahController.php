<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sampah;

class SampahController extends Controller
{
    public function daftar(Request $request)
    {
        $activePage = 'sampah';
        $pageTitle = 'Daftar Sampah';
        $flash = '';
        $flashType = '';

        // Handle delete request
        if ($request->isMethod('post') && $request->filled('action') && $request->input('action') === 'delete') {
            if ($request->filled('id')) {
                $id = (int) $request->input('id');
                
                // CSRF validation
                if (hash_equals(session('_token', ''), $request->input('_token', ''))) {
                    // Soft delete: mark as nonaktif
                    Sampah::where('id_jenis_sampah', $id)->update(['status' => 'nonaktif']);
                    $flash = 'Data sampah berhasil dihapus';
                    $flashType = 'success';
                } else {
                    $flash = 'Token keamanan tidak valid';
                    $flashType = 'error';
                }
            }
        }

        // Check session flash
        if (session()->has('flash_message')) {
            $flash = session('flash_message');
            $flashType = session('flash_type', 'info');
            session()->forget('flash_message');
            session()->forget('flash_type');
        }

        // Get waste types from database
        $sampahList = Sampah::where('status', 'aktif')
            ->get(['id_jenis_sampah as id_jenis', 'nama_jenis', 'harga_per_kg', 'stok as stok_kg', 'status'])
            ->toArray();

        return view('admin.daftar_sampah', compact(
            'activePage',
            'pageTitle',
            'flash',
            'flashType',
            'sampahList'
        ));
    }
}
