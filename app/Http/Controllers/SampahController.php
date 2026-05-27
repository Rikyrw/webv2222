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
        $sampahList = [];
        $databaseError = null;

        try {
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

            $sampahList = $this->fetchSampahList();
        } catch (\Exception $e) {
            \Log::error('SampahController Database Error: ' . $e->getMessage());
            $databaseError = 'Tidak dapat terhubung ke database. Periksa koneksi internet Anda.';
            $sampahList = [];
        }

        return view('admin.daftar_sampah', compact(
            'activePage',
            'pageTitle',
            'flash',
            'flashType',
            'sampahList',
            'databaseError'
        ));
    }

    public function create()
    {
        $activePage = 'sampah';
        $pageTitle = 'Tambah Jenis Sampah';
        return view('admin.tambah_sampah', compact('activePage', 'pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:100',
            'harga_per_kg' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        try {
            $payload = [
                'nama_jenis' => $request->nama_jenis,
                'harga_per_kg' => (int) round((float) $request->harga_per_kg),
                'stok' => $request->stok,
                'status' => 'aktif',
                'id_admin' => session('admin_id'),
            ];

            Sampah::create($payload);

            return redirect()->route('admin.sampah.daftar')->with('flash_message', 'Jenis sampah berhasil ditambahkan')->with('flash_type', 'success');
        } catch (\Exception $e) {
            \Log::error('SampahController Store Error: ' . $e->getMessage());
            return redirect()->back()->with('flash_message', 'Gagal menambahkan jenis sampah')->with('flash_type', 'error');
        }
    }

    public function edit($id)
    {
        $sampah = $this->fetchSampahById($id);
        if (!$sampah) {
            abort(404);
        }
        $sampah = (object) $sampah;
        $activePage = 'sampah';
        $pageTitle = 'Edit Jenis Sampah';
        return view('admin.edit_sampah', compact('activePage', 'pageTitle', 'sampah'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jenis' => 'required|string|max:100',
            'harga_per_kg' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        try {
            $payload = [
                'nama_jenis' => $request->nama_jenis,
                'harga_per_kg' => (int) round((float) $request->harga_per_kg),
                'stok' => $request->stok,
                'status' => $request->status,
            ];

            Sampah::where('id_jenis_sampah', $id)->update($payload);

            return redirect()->route('admin.sampah.daftar')->with('flash_message', 'Jenis sampah berhasil diperbarui')->with('flash_type', 'success');
        } catch (\Exception $e) {
            \Log::error('SampahController Update Error: ' . $e->getMessage());
            return redirect()->back()->with('flash_message', 'Gagal memperbarui jenis sampah')->with('flash_type', 'error');
        }
    }

    private function fetchSampahList(): array
    {
        return Sampah::where('status', 'aktif')
            ->orderBy('nama_jenis')
            ->get(['id_jenis_sampah', 'nama_jenis', 'harga_per_kg', 'stok', 'status'])
            ->map(fn (Sampah $item): array => [
                'id_jenis' => $item->id_jenis_sampah,
                'nama_jenis' => $item->nama_jenis,
                'harga_per_kg' => (int) round((float) $item->harga_per_kg),
                'stok_kg' => $item->stok,
                'status' => $item->status,
            ])
            ->all();
    }

    private function fetchSampahById($id): ?array
    {
        $item = Sampah::find($id);
        if (!$item) {
            return null;
        }

        $data = $item->toArray();
        $data['harga_per_kg'] = (int) round((float) ($data['harga_per_kg'] ?? 0));

        return $data;
    }
}
