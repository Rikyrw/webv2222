<?php

namespace App\Http\Controllers;

use App\Models\DetailSetor;
use App\Models\FotoSetor;
use App\Models\Nasabah;
use App\Models\Sampah;
use App\Models\TopupSaldo;
use App\Models\TransaksiPenarikan;
use App\Models\TransaksiSetor;
use App\Services\MobileNasabahTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MobileNasabahDataController extends Controller
{
    private const DEACTIVATED_LOGIN_MESSAGE = 'Akun Anda sedang nonaktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.';

    private const INACTIVE_LOGIN_MESSAGE = 'Akun Anda belum aktif. Silakan hubungi CS GreenPoint untuk bantuan lebih lanjut.';

    public function __construct(
        private MobileNasabahTokenService $tokens,
    ) {}

    public function profile(Request $request): JsonResponse
    {
        $nasabah = $this->authenticatedNasabah($request);
        if ($nasabah) {
            return response()->json([
                'data' => $this->profileArray($nasabah->fresh() ?? $nasabah),
            ]);
        }

        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        return response()->json([
            'data' => $this->findNasabahByEmail(strtolower(trim($validated['email']))),
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:255'],
            'include_email_match' => ['sometimes', 'boolean'],
        ]);

        return response()->json([
            'data' => $this->findNasabahByIdentifier(
                trim($validated['identifier']),
                $request->boolean('include_email_match', true),
            ),
        ]);
    }

    public function emailAvailability(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        return response()->json([
            'exists' => $this->findNasabahByEmail(strtolower(trim($validated['email']))) !== null,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'old_email' => ['nullable', 'email'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'alamat' => ['required', 'string'],
            'no_hp' => ['required', 'string', 'max:20'],
        ]);

        $authenticated = $this->authenticatedNasabah($request);
        $oldEmail = strtolower(trim($validated['old_email'] ?? (string) $authenticated?->email));
        $newEmail = strtolower(trim($validated['email']));

        $nasabah = $authenticated ?? Nasabah::where('email', $oldEmail)->first();
        if (!$nasabah) {
            return response()->json(['message' => 'Data nasabah tidak ditemukan.'], 404);
        }

        if ($newEmail !== $oldEmail && Nasabah::where('email', $newEmail)->where('id_nasabah', '!=', $nasabah->id_nasabah)->exists()) {
            return response()->json([
                'message' => 'Email sudah digunakan.',
                'errors' => ['email' => ['Email sudah digunakan.']],
            ], 422);
        }

        $username = trim($validated['user_name']);
        if (Nasabah::where('user_name', $username)->where('id_nasabah', '!=', $nasabah->id_nasabah)->exists()) {
            return response()->json([
                'message' => 'Username sudah digunakan.',
                'errors' => ['user_name' => ['Username sudah digunakan.']],
            ], 422);
        }

        $nasabah->update([
            'nama_lengkap' => trim($validated['nama_lengkap']),
            'user_name' => $username,
            'email' => $newEmail,
            'alamat' => trim($validated['alamat']),
            'no_hp' => trim($validated['no_hp']),
        ]);

        return response()->json([
            'message' => 'Profil berhasil diperbarui.',
            'data' => $this->profileArray($nasabah->fresh()),
        ]);
    }

    public function mirrorProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firebase_uid' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'user_name' => ['nullable', 'string', 'max:50'],
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'alamat' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'photo_url' => ['nullable', 'string', 'max:2048'],
            'google_id' => ['nullable', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:50'],
            'saldo' => ['nullable', 'numeric'],
        ]);

        $email = strtolower(trim($validated['email']));
        $provider = $this->filledText($validated['provider'] ?? null) ?: 'firebase';
        $existing = Nasabah::where('email', $email)->first();

        if ($existing && ($statusMessage = $this->accountStatusMessage($existing->getAttributes()))) {
            return response()->json([
                'message' => $statusMessage,
                'account_status' => $existing->status,
            ], 403);
        }

        $payload = [
            'user_name' => $this->filledText($validated['user_name'] ?? null) ?: explode('@', $email)[0],
            'nama_lengkap' => $this->filledText($validated['nama_lengkap'] ?? null) ?: explode('@', $email)[0],
            'email' => $email,
            'no_hp' => $this->filledText($validated['no_hp'] ?? null),
            'alamat' => $this->filledText($validated['alamat'] ?? null) ?: '',
            'photo_url' => $this->filledText($validated['photo_url'] ?? null),
            'google_id' => $this->filledText($validated['google_id'] ?? null),
            'provider' => $provider,
        ];

        if ($provider === 'google' || $payload['google_id'] !== null) {
            $payload['email_verified_at'] = now();
            $payload['email_verification_token_hash'] = null;
            $payload['email_verification_expires_at'] = null;
        }

        $payload = array_filter($payload, fn ($value) => $value !== null);

        if ($existing) {
            $existing->update($payload);
            $nasabah = $existing->fresh();
        } else {
            $nasabah = Nasabah::create([
                ...$payload,
                'status' => 'aktif',
                'saldo' => $validated['saldo'] ?? 0,
                'created_at' => now(),
                'password' => 'firebase-auth:'.$validated['firebase_uid'],
            ]);
        }

        return response()->json([
            'data' => $this->profileArray($nasabah),
            ...$this->tokens->payload($nasabah, $request->input('device_name')),
        ]);
    }

    public function markPasswordManaged(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'firebase_uid' => ['required', 'string', 'max:255'],
        ]);

        $nasabah = $this->authenticatedNasabah($request);
        if (!$nasabah) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $nasabah->update([
            'password' => 'firebase-auth:'.$validated['firebase_uid'],
        ]);

        return response()->json(['message' => 'OK']);
    }

    public function wasteTypes(): JsonResponse
    {
        $items = Sampah::where('status', 'aktif')
            ->orderBy('nama_jenis')
            ->get()
            ->map(fn (Sampah $row): array => [
                'id' => (int) $row->id_jenis_sampah,
                'name' => (string) $row->nama_jenis,
                'price' => (float) $row->harga_per_kg,
            ])
            ->values()
            ->all();

        return response()->json(['data' => $items]);
    }

    public function setorHistory(Request $request): JsonResponse
    {
        return $this->setorHistoryPage($request);
    }

    public function ppobTransactions(Request $request): JsonResponse
    {
        return $this->ppobHistoryPage($request);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nasabah_id' => ['nullable', 'integer', 'min:1'],
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d'],
        ]);

        $nasabahId = $this->authenticatedNasabahId($request);
        $setorRows = TransaksiSetor::with('detailSetor')
            ->where('id_nasabah', $nasabahId)
            ->whereBetween('tanggal_setor', [$validated['from'], $validated['to']])
            ->get();
        $ppobRows = TransaksiPenarikan::where('id_nasabah', $nasabahId)
            ->whereBetween('tanggal_pengajuan', [$validated['from'], $validated['to']])
            ->get();

        $recentSetorRows = TransaksiSetor::with('detailSetor')
            ->where('id_nasabah', $nasabahId)
            ->orderByDesc('tanggal_setor')
            ->limit(3)
            ->get()
            ->map(fn (TransaksiSetor $row): array => $this->setorArray($row))
            ->all();

        $recentPpobRows = TransaksiPenarikan::where('id_nasabah', $nasabahId)
            ->orderByDesc('tanggal_pengajuan')
            ->limit(8)
            ->get()
            ->filter(fn (TransaksiPenarikan $row): bool => $this->isPpobTransaction($row))
            ->take(3)
            ->map(fn (TransaksiPenarikan $row): array => $this->ppobArray($row))
            ->values()
            ->all();

        return response()->json([
            'stats' => $this->buildDashboardStats($setorRows, $ppobRows),
            'recent_setor' => $recentSetorRows,
            'recent_ppob' => $recentPpobRows,
        ]);
    }

    public function topupHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nasabah_id' => ['nullable', 'integer', 'min:1'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = TopupSaldo::where('id_nasabah', $this->authenticatedNasabahId($request))
            ->orderByDesc('created_at')
            ->limit((int) ($validated['limit'] ?? 5));

        if (!empty($validated['date'])) {
            $start = Carbon::createFromFormat('Y-m-d', $validated['date'])->startOfDay();
            $end = (clone $start)->addDay();
            $query->where('created_at', '>=', $start)->where('created_at', '<', $end);
        }

        return response()->json([
            'data' => $query->get()->map(fn (TopupSaldo $row): array => $row->toArray())->all(),
        ]);
    }

    public function storePpob(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_nasabah' => ['nullable', 'integer', 'min:1'],
            'jenis_penukaran' => ['required', 'string', 'max:50'],
            'nominal' => ['required', 'integer', 'min:1000'],
            'deskripsi' => ['required', 'string', 'max:255'],
        ]);

        $nasabah = $this->authenticatedNasabah($request);
        if (!$nasabah) {
            return response()->json(['message' => 'Data nasabah tidak ditemukan.'], 404);
        }

        if ((float) $nasabah->saldo < (int) $validated['nominal']) {
            return response()->json(['message' => 'Saldo tidak mencukupi.'], 422);
        }

        $row = TransaksiPenarikan::create([
            'id_nasabah' => (int) $nasabah->id_nasabah,
            'jenis_penukaran' => trim($validated['jenis_penukaran']),
            'nominal' => (int) $validated['nominal'],
            'status' => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
            'deskripsi' => trim($validated['deskripsi']),
        ]);

        return response()->json([
            'message' => 'Permintaan berhasil dikirim.',
            'data' => $this->ppobArray($row),
        ], 201);
    }

    public function storeSetor(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_nasabah' => ['nullable', 'integer', 'min:1'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_jenis' => ['required', 'integer', 'min:1'],
            'items.*.berat_kg' => ['required', 'numeric', 'min:1'],
            'items.*.photos' => ['required', 'array', 'min:1', 'max:3'],
        ]);

        try {
            $items = $this->buildSetorItems($validated['items']);
            if ($items['error'] !== null) {
                return response()->json(['message' => $items['error']], 422);
            }

            $nasabahId = $this->authenticatedNasabahId($request);
            $transaksiId = DB::transaction(function () use ($nasabahId, $items): int {
                $transaksi = TransaksiSetor::create([
                    'id_nasabah' => $nasabahId,
                    'total_nilai' => $items['total_nilai'],
                    'tanggal_setor' => now()->toDateString(),
                    'status' => 'pending',
                ]);

                foreach ($items['items'] as $itemIndex => $item) {
                    $detail = DetailSetor::create([
                        'id_transaksi_setor' => $transaksi->id_transaksi_setor,
                        'id_jenis' => $item['id_jenis'],
                        'berat_kg' => $item['berat_kg'],
                        'harga_kg' => $item['harga_kg'],
                        'subtotal' => $item['subtotal'],
                        'status_item' => 'pending',
                    ]);

                    foreach ($item['photos'] as $photoIndex => $photo) {
                        $path = 'setor/'.$nasabahId.'/'.now()->timestamp.'_'.$transaksi->id_transaksi_setor.'_'.$item['id_jenis'].'_'.$itemIndex.'_'.$photoIndex.'.'.$photo['extension'];
                        $url = $this->storePublicImage($path, $photo['bytes']);

                        FotoSetor::create([
                            'id_transaksi_setor' => $transaksi->id_transaksi_setor,
                            'id_detail_setor' => $detail->id_detail_setor,
                            'id_jenis' => $item['id_jenis'],
                            'foto_url' => $url,
                            'created_at' => now(),
                        ]);
                    }
                }

                return (int) $transaksi->id_transaksi_setor;
            });

            return response()->json([
                'message' => 'Setor sampah berhasil diajukan.',
                'id_transaksi_setor' => $transaksiId,
            ], 201);
        } catch (\Throwable $exception) {
            Log::warning('Mobile setor submission failed.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Gagal mengajukan setor sampah.'], 500);
        }
    }

    private function setorHistoryPage(Request $request): JsonResponse
    {
        $validated = $this->historyValidation($request);
        $query = TransaksiSetor::with('detailSetor.sampah')
            ->where('id_nasabah', $this->authenticatedNasabahId($request))
            ->orderByDesc('tanggal_setor');

        if ($request->boolean('pending_only')) {
            $query->whereIn('status', ['pending', 'menunggu', 'diproses']);
        } elseif (!empty($validated['from']) && !empty($validated['to'])) {
            $query->whereBetween('tanggal_setor', [$validated['from'], $validated['to']]);
        }

        return $this->pagedResponse($query, (int) ($validated['page'] ?? 0), (int) ($validated['page_size'] ?? 8), fn (TransaksiSetor $row): array => $this->setorArray($row));
    }

    private function ppobHistoryPage(Request $request): JsonResponse
    {
        $validated = $this->historyValidation($request);
        $query = TransaksiPenarikan::where('id_nasabah', $this->authenticatedNasabahId($request))
            ->orderByDesc('tanggal_pengajuan');

        if ($request->boolean('pending_only')) {
            $query->whereIn('status', ['pending', 'menunggu', 'diproses']);
        } elseif (!empty($validated['from']) && !empty($validated['to'])) {
            $query->whereBetween('tanggal_pengajuan', [$validated['from'], $validated['to']]);
        }

        return $this->pagedResponse($query, (int) ($validated['page'] ?? 0), (int) ($validated['page_size'] ?? 8), fn (TransaksiPenarikan $row): array => $this->ppobArray($row));
    }

    private function historyValidation(Request $request): array
    {
        return $request->validate([
            'nasabah_id' => ['nullable', 'integer', 'min:1'],
            'page' => ['nullable', 'integer', 'min:0'],
            'page_size' => ['nullable', 'integer', 'min:1', 'max:50'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d'],
            'pending_only' => ['nullable', 'boolean'],
        ]);
    }

    private function pagedResponse($query, int $page, int $pageSize, callable $mapper): JsonResponse
    {
        $offset = $page * $pageSize;
        $rows = $query->offset($offset)->limit($pageSize + 1)->get();

        return response()->json([
            'items' => $rows->take($pageSize)->map($mapper)->values()->all(),
            'has_next_page' => $rows->count() > $pageSize,
        ]);
    }

    private function authenticatedNasabah(Request $request): ?Nasabah
    {
        $user = $request->user();

        if ($user instanceof Nasabah && ($statusMessage = $this->accountStatusMessage($user->getAttributes()))) {
            abort(403, $statusMessage);
        }

        return $user instanceof Nasabah ? $user : null;
    }

    private function authenticatedNasabahId(Request $request): int
    {
        $nasabah = $this->authenticatedNasabah($request);
        if (!$nasabah) {
            abort(401, 'Unauthenticated.');
        }

        return (int) $nasabah->id_nasabah;
    }

    private function findNasabahByIdentifier(string $identifier, bool $includeEmailMatch = true): ?array
    {
        $value = trim($identifier);
        if ($value === '') {
            return null;
        }

        if ($includeEmailMatch) {
            $record = $this->findNasabahByEmail(strtolower($value)) ?: $this->findNasabahByEmailIlike($value);
            if ($record) {
                return $record;
            }
        }

        return $this->findNasabahByUsername($value) ?: $this->findNasabahByUsernameIlike($value);
    }

    private function findNasabahByEmail(string $email): ?array
    {
        return $this->profileArray(Nasabah::where('email', $email)->first());
    }

    private function findNasabahByEmailIlike(string $email): ?array
    {
        return $this->profileArray(Nasabah::whereRaw('LOWER(email) = ?', [strtolower($email)])->first());
    }

    private function findNasabahByUsername(string $username): ?array
    {
        return $this->profileArray(Nasabah::where('user_name', $username)->first());
    }

    private function findNasabahByUsernameIlike(string $username): ?array
    {
        return $this->profileArray(Nasabah::whereRaw('LOWER(user_name) = ?', [strtolower($username)])->first());
    }

    private function buildSetorItems(array $items): array
    {
        $ids = collect($items)
            ->map(fn (array $item): int => (int) ($item['id_jenis'] ?? 0))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $priceMap = Sampah::whereIn('id_jenis_sampah', $ids)
            ->where('status', 'aktif')
            ->pluck('harga_per_kg', 'id_jenis_sampah')
            ->map(fn ($price): float => (float) $price)
            ->all();

        $total = 0;
        $builtItems = [];

        foreach ($items as $item) {
            $idJenis = (int) $item['id_jenis'];
            $weight = (float) $item['berat_kg'];
            if (!isset($priceMap[$idJenis])) {
                return ['error' => 'Jenis sampah tidak ditemukan atau tidak aktif.', 'total_nilai' => 0, 'items' => []];
            }

            $photos = [];
            foreach (($item['photos'] ?? []) as $photo) {
                $decoded = $this->decodePhotoPayload($photo);
                if ($decoded === null) {
                    return ['error' => 'Foto sampah harus berupa JPG/PNG valid dan maksimal 3 MB.', 'total_nilai' => 0, 'items' => []];
                }

                $photos[] = $decoded;
            }

            $subtotal = (int) round($priceMap[$idJenis] * $weight);
            $total += $subtotal;
            $builtItems[] = [
                'id_jenis' => $idJenis,
                'berat_kg' => $weight,
                'harga_kg' => $priceMap[$idJenis],
                'subtotal' => $subtotal,
                'photos' => $photos,
            ];
        }

        return ['error' => null, 'total_nilai' => $total, 'items' => $builtItems];
    }

    private function decodePhotoPayload(mixed $photo): ?array
    {
        $data = is_array($photo) ? (string) ($photo['data'] ?? '') : (string) $photo;
        $mimeType = is_array($photo) ? (string) ($photo['mime_type'] ?? '') : '';
        $extension = is_array($photo) ? strtolower((string) ($photo['extension'] ?? '')) : '';

        if (preg_match('/^data:image\/(jpeg|jpg|png);base64,/i', $data, $matches)) {
            $mimeType = 'image/'.strtolower($matches[1]);
            $extension = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
            $data = preg_replace('/^data:image\/(jpeg|jpg|png);base64,/i', '', $data) ?: '';
        }

        if (!in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png'], true)) {
            $mimeType = $extension === 'png' ? 'image/png' : 'image/jpeg';
        }

        $bytes = base64_decode($data, true);
        if ($bytes === false || strlen($bytes) === 0 || strlen($bytes) > 3 * 1024 * 1024) {
            return null;
        }

        if (@getimagesizefromstring($bytes) === false) {
            return null;
        }

        return [
            'bytes' => $bytes,
            'mime_type' => $mimeType === 'image/jpg' ? 'image/jpeg' : $mimeType,
            'extension' => $mimeType === 'image/png' ? 'png' : 'jpg',
        ];
    }

    private function storePublicImage(string $path, string $bytes): string
    {
        Storage::disk('public')->put($path, $bytes);

        return Storage::disk('public')->url($path);
    }

    private function buildDashboardStats($setorRows, $ppobRows): array
    {
        $totalWeightKg = 0.0;
        $completedSetorValue = 0;
        $waitingSetorCount = 0;
        $completedSetorCount = 0;
        $rejectedSetorCount = 0;

        foreach ($setorRows as $row) {
            $bucket = $this->setorStatusBucket($row->status ?? null);
            if ($bucket === 'waiting') {
                $waitingSetorCount++;
            } elseif ($bucket === 'completed') {
                $completedSetorCount++;
                $completedSetorValue += (int) round((float) $row->total_nilai);
            } elseif ($bucket === 'rejected') {
                $rejectedSetorCount++;
            }

            foreach ($row->detailSetor as $detail) {
                $totalWeightKg += (float) $detail->berat_kg;
            }
        }

        $ppobCount = 0;
        $ppobAmount = 0;
        $withdrawalCount = 0;
        $withdrawalAmount = 0;

        foreach ($ppobRows as $row) {
            $nominal = (int) round((float) $row->nominal);
            if ($this->isPpobTransaction($row)) {
                $ppobCount++;
                $ppobAmount += $nominal;
            } else {
                $withdrawalCount++;
                $withdrawalAmount += $nominal;
            }
        }

        return [
            'setorCount' => count($setorRows),
            'totalWeightKg' => $totalWeightKg,
            'completedSetorValue' => $completedSetorValue,
            'ppobCount' => $ppobCount,
            'ppobAmount' => $ppobAmount,
            'withdrawalCount' => $withdrawalCount,
            'withdrawalAmount' => $withdrawalAmount,
            'waitingSetorCount' => $waitingSetorCount,
            'completedSetorCount' => $completedSetorCount,
            'rejectedSetorCount' => $rejectedSetorCount,
        ];
    }

    private function setorArray(TransaksiSetor $row): array
    {
        return [
            'id_transaksi_setor' => $row->id_transaksi_setor,
            'total_nilai' => (float) $row->total_nilai,
            'tanggal_setor' => $row->tanggal_setor?->toDateString(),
            'status' => $row->status,
            'detail_setor' => $row->detailSetor->map(fn (DetailSetor $detail): array => [
                'berat_kg' => (float) $detail->berat_kg,
                'harga_kg' => (float) $detail->harga_kg,
                'subtotal' => (float) $detail->subtotal,
                'jenis_sampah' => [
                    'nama_jenis' => $detail->sampah?->nama_jenis,
                ],
            ])->all(),
        ];
    }

    private function ppobArray(TransaksiPenarikan $row): array
    {
        return [
            'id_penarikan' => $row->id_penarikan,
            'jenis_penukaran' => $row->jenis_penukaran,
            'nominal' => (float) $row->nominal,
            'status' => $row->status,
            'tanggal_pengajuan' => $row->tanggal_pengajuan?->toDateString(),
            'deskripsi' => $row->deskripsi,
        ];
    }

    private function profileArray(?Nasabah $row, bool $includeSensitive = false): ?array
    {
        if (!$row) {
            return null;
        }

        $data = [
            'id_nasabah' => $row->id_nasabah,
            'user_name' => $row->user_name,
            'nama_lengkap' => $row->nama_lengkap,
            'email' => $row->email,
            'no_hp' => $row->no_hp,
            'alamat' => $row->alamat,
            'photo_url' => $row->photo_url,
            'google_id' => $row->google_id,
            'google_sub' => $row->google_sub,
            'provider' => $row->provider,
            'saldo' => (float) $row->saldo,
            'email_verified_at' => $row->email_verified_at?->toIso8601String(),
        ];

        if ($includeSensitive) {
            $data['password'] = $row->getAttribute('password');
            $data['email_verification_token_hash'] = $row->email_verification_token_hash;
            $data['email_verification_expires_at'] = $row->email_verification_expires_at?->toIso8601String();
            $data['email_verification_sent_at'] = $row->email_verification_sent_at?->toIso8601String();
        }

        return $data;
    }

    private function setorStatusBucket(mixed $status): ?string
    {
        $value = strtolower(trim((string) $status));

        if (in_array($value, ['pending', 'menunggu', 'diproses', 'process'], true)) {
            return 'waiting';
        }

        if (in_array($value, ['success', 'approved', 'berhasil', 'selesai', 'sukses'], true)) {
            return 'completed';
        }

        if (in_array($value, ['rejected', 'reject', 'ditolak', 'failed', 'gagal', 'cancelled', 'canceled'], true)) {
            return 'rejected';
        }

        return null;
    }

    private function isPpobTransaction(TransaksiPenarikan $row): bool
    {
        $description = strtolower(trim((string) $row->deskripsi));
        if (str_starts_with($description, 'emoney:')
            || str_starts_with($description, 'pln:')
            || str_starts_with($description, 'pulsa:')) {
            return true;
        }

        $type = strtolower(trim((string) $row->jenis_penukaran));

        return in_array($type, ['dana', 'gopay', 'ovo', 'shopeepay', 'linkaja', 'emoney', 'e-money', 'pln', 'pulsa'], true);
    }

    private function filledText(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);

        return $text === '' ? null : $text;
    }

    private function accountStatusMessage(array $user): ?string
    {
        $status = strtolower(trim((string) ($user['status'] ?? 'aktif')));

        if ($status === '' || $status === 'aktif') {
            return null;
        }

        if (in_array($status, ['nonaktif', 'ditolak', 'inactive', 'disabled', 'banned'], true)) {
            return self::DEACTIVATED_LOGIN_MESSAGE;
        }

        return self::INACTIVE_LOGIN_MESSAGE;
    }
}
