<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sampah;
use Illuminate\Support\Facades\Http;

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
                'harga_per_kg' => $request->harga_per_kg,
                'stok' => $request->stok,
                'status' => 'aktif',
                'id_admin' => session('admin_id'),
            ];

            $response = $this->supabaseRequest('post', '/rest/v1/jenis_sampah', $payload, true);

            if (!$response->successful()) {
                throw new \RuntimeException('Supabase insert gagal');
            }

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
                'harga_per_kg' => $request->harga_per_kg,
                'stok' => $request->stok,
                'status' => $request->status,
            ];

            $response = $this->supabaseRequest(
                'patch',
                '/rest/v1/jenis_sampah?id_jenis_sampah=eq.' . $id,
                $payload,
                true
            );

            if (!$response->successful()) {
                throw new \RuntimeException('Supabase update gagal');
            }

            return redirect()->route('admin.sampah.daftar')->with('flash_message', 'Jenis sampah berhasil diperbarui')->with('flash_type', 'success');
        } catch (\Exception $e) {
            \Log::error('SampahController Update Error: ' . $e->getMessage());
            return redirect()->back()->with('flash_message', 'Gagal memperbarui jenis sampah')->with('flash_type', 'error');
        }
    }

    private function fetchSampahList(): array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/jenis_sampah?select=id_jenis_sampah,nama_jenis,harga_per_kg,stok,status&status=eq.aktif',
            null,
            false
        );

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json();
        if (!is_array($items)) {
            return [];
        }

        $mapped = [];
        foreach ($items as $item) {
            $mapped[] = [
                'id_jenis' => $item['id_jenis_sampah'] ?? null,
                'nama_jenis' => $item['nama_jenis'] ?? null,
                'harga_per_kg' => $item['harga_per_kg'] ?? null,
                'stok_kg' => $item['stok'] ?? null,
                'status' => $item['status'] ?? null,
            ];
        }

        return $mapped;
    }

    private function fetchSampahById($id): ?array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/jenis_sampah?select=*&id_jenis_sampah=eq.' . $id . '&limit=1',
            null,
            false
        );

        if (!$response->successful()) {
            return null;
        }

        $items = $response->json();
        if (!is_array($items) || count($items) === 0) {
            return null;
        }

        return $items[0];
    }

    private function supabaseRequest(string $method, string $path, ?array $payload, bool $returnRepresentation)
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');

        $request = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
        ]);

        if ($returnRepresentation) {
            $request = $request->withHeaders([
                'Prefer' => 'return=representation',
            ]);
        }

        $url = $supabaseUrl . $path;

        if ($method === 'get') {
            return $request->get($url);
        }

        if ($method === 'post') {
            return $request->post($url, $payload ?? []);
        }

        if ($method === 'patch') {
            return $request->patch($url, $payload ?? []);
        }

        if ($method === 'delete') {
            return $request->delete($url);
        }

        return $request->get($url);
    }
}
