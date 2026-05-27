<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TransaksiPenarikan;
use App\Models\TransaksiSetor;
use Illuminate\Http\Request;

class NasabahController extends Controller
{
    public function daftar(Request $request)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Daftar Nasabah';
        $flash = '';
        $databaseError = null;

        try {
            if ($request->isMethod('post') && $request->filled('action') && $request->filled('id_nasabah')) {
                $id = (int) $request->input('id_nasabah');
                $status = match ($request->input('action')) {
                    'aktifkan' => 'aktif',
                    'tolak' => 'nonaktif',
                    default => null,
                };

                if (!hash_equals(session('_token', ''), $request->input('_token', ''))) {
                    $flash = 'Token keamanan tidak valid.';
                } elseif ($status) {
                    Nasabah::where('id_nasabah', $id)->update(['status' => $status]);
                    $flash = 'Status nasabah berhasil diperbarui.';
                } else {
                    $flash = 'Aksi tidak dikenali.';
                }
            }

            if (session()->has('flash_nasabah')) {
                $flash = session()->pull('flash_nasabah');
            }

            $page = max(1, (int) $request->get('page', 1));
            $result = $this->fetchNasabahList($page, 10);
            $nasabahs = $result['items'];
            $nasabahsMeta = $result['meta'];
        } catch (\Exception $e) {
            \Log::error('NasabahController Database Error: '.$e->getMessage());
            $databaseError = 'Tidak dapat terhubung ke database. Periksa koneksi database.';
            $nasabahs = [];
            $nasabahsMeta = $this->emptyMeta();
        }

        return view('admin.daftar_nasabah', compact(
            'activePage',
            'pageTitle',
            'flash',
            'nasabahs',
            'nasabahsMeta',
            'databaseError'
        ));
    }

    public function edit(int $id)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Edit Nasabah';
        $nasabah = $this->fetchNasabahById($id);

        if (!$nasabah) {
            abort(404);
        }

        return view('admin.edit_nasabah', compact('activePage', 'pageTitle', 'nasabah'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'alamat' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:30',
            'saldo' => 'nullable|numeric|min:0',
            'status' => 'required|in:aktif,menunggu,nonaktif',
            'user_name' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:150',
        ]);

        try {
            Nasabah::where('id_nasabah', $id)->update([
                'nama_lengkap' => $request->input('nama_lengkap'),
                'alamat' => $request->input('alamat') ?: null,
                'no_hp' => $request->input('no_hp') ?: null,
                'saldo' => $request->input('saldo') ?? 0,
                'status' => $request->input('status'),
                'user_name' => $request->input('user_name') ?: null,
                'email' => $request->input('email') ?: null,
            ]);

            return redirect()->route('admin.nasabah.daftar')->with('flash_nasabah', 'Data nasabah berhasil diperbarui.');
        } catch (\Exception $e) {
            \Log::error('NasabahController Update Error: '.$e->getMessage());

            return redirect()->back()->with('flash_nasabah', 'Gagal memperbarui data nasabah.');
        }
    }

    public function destroy(int $id)
    {
        try {
            Nasabah::where('id_nasabah', $id)->delete();

            return redirect()->route('admin.nasabah.daftar')->with('flash_nasabah', 'Nasabah berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('NasabahController Delete Error: '.$e->getMessage());

            return redirect()->back()->with('flash_nasabah', 'Gagal menghapus nasabah.');
        }
    }

    public function riwayat(int $id)
    {
        $activePage = 'nasabah';
        $pageTitle = 'Riwayat Nasabah';
        $nasabah = $this->fetchNasabahById($id);

        if (!$nasabah) {
            abort(404);
        }

        $databaseError = null;

        try {
            $setorList = TransaksiSetor::with('detailSetor.sampah')
                ->where('id_nasabah', $id)
                ->orderByDesc('tanggal_setor')
                ->limit(20)
                ->get()
                ->map(fn (TransaksiSetor $item): array => [
                    'id' => $item->id_transaksi_setor,
                    'tanggal' => $item->tanggal_setor?->toDateString(),
                    'tanggal_proses' => $item->tanggal_proses?->toDateString(),
                    'total_berat' => $item->detailSetor->sum(fn ($detail) => (float) $detail->berat_kg),
                    'total_nilai' => (float) $item->total_nilai,
                    'jenis' => $this->jenisLabel($item),
                    'status' => $item->status ?? 'menunggu',
                ])
                ->all();

            $penarikanList = TransaksiPenarikan::where('id_nasabah', $id)
                ->orderByDesc('tanggal_pengajuan')
                ->limit(20)
                ->get()
                ->map(fn (TransaksiPenarikan $item): array => [
                    'id' => $item->id_penarikan,
                    'jenis' => $item->jenis_penukaran ?: 'Penarikan',
                    'nominal' => (float) $item->nominal,
                    'status' => $item->status ?? 'menunggu',
                    'deskripsi' => $item->deskripsi ?: '-',
                    'tanggal' => $item->tanggal_pengajuan?->toDateString(),
                    'tanggal_proses' => $item->tanggal_proses?->toDateString(),
                ])
                ->all();
        } catch (\Exception $e) {
            \Log::error('NasabahController Riwayat Error: '.$e->getMessage());
            $databaseError = 'Tidak dapat mengambil riwayat nasabah.';
            $setorList = [];
            $penarikanList = [];
        }

        return view('admin.riwayat_nasabah', compact(
            'activePage',
            'pageTitle',
            'nasabah',
            'setorList',
            'penarikanList',
            'databaseError'
        ));
    }

    private function fetchNasabahList(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $items = Nasabah::orderByDesc('created_at')
            ->orderByDesc('id_nasabah')
            ->offset($offset)
            ->limit($perPage + 1)
            ->get();

        $hasNext = $items->count() > $perPage;
        $items = $items->take($perPage);

        return [
            'items' => $items->map(fn (Nasabah $item): array => [
                'id_nasabah' => $item->id_nasabah,
                'user_name' => $item->user_name,
                'nama_nasabah' => $item->nama_lengkap,
                'email' => $item->email,
                'alamat' => $item->alamat ?: '-',
                'no_hp' => $item->no_hp ?: '-',
                'saldo' => $item->saldo ?? 0,
                'status_akun' => $item->status ?? 'verifikasi',
                'tanggal_daftar' => $item->created_at ? (string) $item->created_at : '-',
                'google_id' => $item->google_id,
                'photo_url' => $item->photo_url,
                'provider' => $item->provider,
            ])->all(),
            'meta' => [
                'page' => $page,
                'has_next' => $hasNext,
                'has_prev' => $page > 1,
                'offset' => $offset,
            ],
        ];
    }

    private function fetchNasabahById(int $id): ?array
    {
        return Nasabah::where('id_nasabah', $id)->first()?->getAttributes();
    }

    private function jenisLabel(TransaksiSetor $setor): string
    {
        $items = $setor->detailSetor
            ->map(fn ($detail) => $detail->sampah?->nama_jenis)
            ->filter()
            ->unique()
            ->values();

        return $items->isEmpty() ? 'N/A' : $items->implode(', ');
    }

    private function emptyMeta(): array
    {
        return [
            'page' => 1,
            'has_next' => false,
            'has_prev' => false,
            'offset' => 0,
        ];
    }
}
