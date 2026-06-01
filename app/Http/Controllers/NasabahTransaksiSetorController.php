<?php

namespace App\Http\Controllers;

use App\Models\DetailSetor;
use App\Models\FotoSetor;
use App\Models\Nasabah;
use App\Models\Sampah;
use App\Models\TransaksiSetor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NasabahTransaksiSetorController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'setor';

        $user = null;
        $id = session('id_nasabah');

        if ($id) {
            try {
                $model = Nasabah::find($id);
                if ($model) {
                    $row = $model->getAttributes();
                    $user = [
                        'id_nasabah' => $row['id_nasabah'] ?? $id,
                        'nama_nasabah' => $row['nama_lengkap'] ?? ($row['nama_nasabah'] ?? session('nama_nasabah')),
                        'alamat' => $row['alamat'] ?? session('alamat'),
                        'saldo' => isset($row['saldo']) ? (float)$row['saldo'] : (session('saldo') ?? 0),
                        'email' => $row['email'] ?? session('email'),
                        'no_hp' => $row['no_hp'] ?? session('no_hp'),
                        'username' => $row['user_name'] ?? session('username'),
                        'tanggal_daftar' => $row['created_at'] ?? null,
                    ];

                    // sync session
                    session([
                        'nama_nasabah' => $user['nama_nasabah'],
                        'alamat' => $user['alamat'],
                        'saldo' => $user['saldo'],
                        'email' => $user['email'],
                        'no_hp' => $user['no_hp'],
                        'username' => $user['username'],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch nasabah for setor: '.$e->getMessage());
            }
        }

        // Keep the page usable during a temporary database read failure.
        if (!$user) {
            $user = [
                'id_nasabah' => (int) session('id_nasabah'),
                'nama_nasabah' => session('nama_nasabah') ?? 'User',
                'alamat' => session('alamat') ?? '',
                'saldo' => session('saldo') ?? 0,
                'email' => session('email') ?? '',
                'no_hp' => session('no_hp') ?? '',
                'username' => session('username') ?? '',
            ];
        }

        $waste_types = $this->fetchWasteTypes();

        session([
            'nama_nasabah' => $user['nama_nasabah'],
            'saldo' => $user['saldo'],
            'alamat' => $user['alamat']
        ]);

        // Handle setor transaction submission
        $success = null;
        $error = null;
        if ($request->isMethod('post') && $request->has('submit_transaction')) {
            $waste_items = $request->input('waste_items', []);
            $waste_photos = $request->input('waste_photos', []);

            if (empty($waste_items)) {
                $error = 'Tambahkan minimal 1 item sebelum mengajukan setor';
            } elseif (!$user['id_nasabah']) {
                $error = 'Akun tidak ditemukan. Silakan login ulang.';
            } else {
                $validationError = $this->validateWasteItems($waste_items, $waste_photos);
                if ($validationError) {
                    $error = $validationError;
                } else {
                    try {
                        $calcResult = $this->calculateTotalsFromDatabase($waste_items);
                        if ($calcResult['error']) {
                            $error = $calcResult['error'];
                        } else {
                            $totalNilai = $calcResult['total_nilai'];
                            $detailRows = $calcResult['detail_rows'];

                            DB::transaction(function () use ($user, $totalNilai, $detailRows, $waste_items, $waste_photos): void {
                                $transaksi = TransaksiSetor::create([
                                    'id_nasabah' => intval($user['id_nasabah']),
                                    'total_nilai' => $totalNilai,
                                    'tanggal_setor' => date('Y-m-d'),
                                    'status' => 'menunggu',
                                ]);

                                foreach ($detailRows as $index => $row) {
                                    $detail = DetailSetor::create([
                                        ...$row,
                                        'id_transaksi_setor' => $transaksi->id_transaksi_setor,
                                    ]);

                                    $foto = $waste_photos[$index] ?? null;
                                    if ($foto) {
                                        $idJenis = (int) ($waste_items[$index]['id_jenis'] ?? 0);
                                        $fotoUrl = $this->storeWastePhoto(
                                            (string) $foto,
                                            (int) $user['id_nasabah'],
                                            (int) $transaksi->id_transaksi_setor,
                                            (int) $detail->id_detail_setor,
                                            $idJenis
                                        );

                                        FotoSetor::create([
                                            'id_transaksi_setor' => $transaksi->id_transaksi_setor,
                                            'id_detail_setor' => $detail->id_detail_setor,
                                            'id_jenis' => $idJenis,
                                            'foto_url' => $fotoUrl,
                                            'created_at' => now(),
                                        ]);
                                    }
                                }
                            });

                            session()->forget('validated_waste_photos');
                            $success = 'Transaksi setor sampah berhasil diajukan! Status: Menunggu persetujuan admin.';
                        }
                    } catch (\Exception $e) {
                        \Log::error('Setor transaksi error: ' . $e->getMessage());
                        $error = 'Terjadi kesalahan saat mengajukan setor.';
                    }
                }
            }
        }

        return view('nasabah.transaksi_setor', [
            'activePage' => $activePage,
            'user' => $user,
            'waste_types' => $waste_types,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function detectWastePhoto(Request $request)
    {
        $validated = $request->validate([
            'id_jenis' => ['required', 'integer', 'min:1'],
            'photo' => ['required', 'string'],
        ]);

        $idJenis = (int) $validated['id_jenis'];
        $photo = trim((string) $validated['photo']);

        if (!$this->isValidImageDataUrl($photo)) {
            return response()->json([
                'valid' => false,
                'message' => 'Foto harus berupa gambar JPG atau PNG yang valid.',
            ], 422);
        }

        $selectedWasteType = $this->fetchWasteTypeById($idJenis);
        if (!$selectedWasteType) {
            return response()->json([
                'valid' => false,
                'message' => 'Jenis sampah tidak ditemukan atau tidak aktif.',
            ], 422);
        }

        $wasteTypeNames = array_values(array_filter(array_map(function ($item) {
            return $item['nama_jenis'] ?? null;
        }, $this->fetchWasteTypes())));

        try {
            $detection = $this->detectWastePhotoWithGroq(
                $photo,
                (string) $selectedWasteType['nama_jenis'],
                $wasteTypeNames
            );
        } catch (\Exception $e) {
            \Log::warning('Waste photo detection failed: ' . $e->getMessage());

            return response()->json([
                'valid' => false,
                'message' => 'Foto belum bisa diperiksa otomatis. Coba upload ulang beberapa saat lagi.',
            ], 503);
        }

        if (!$detection['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $detection['message'],
                'detected_type' => $detection['detected_type'],
                'confidence' => $detection['confidence'],
            ], 422);
        }

        $this->rememberValidatedWastePhoto($photo, $idJenis, (string) $selectedWasteType['nama_jenis']);

        return response()->json([
            'valid' => true,
            'message' => 'Foto cocok dengan jenis sampah ' . $selectedWasteType['nama_jenis'] . '.',
            'detected_type' => $detection['detected_type'],
            'confidence' => $detection['confidence'],
        ]);
    }

    private function fetchWasteTypes(): array
    {
        try {
            return Sampah::where('status', 'aktif')
                ->orderBy('nama_jenis')
                ->get(['id_jenis_sampah', 'nama_jenis', 'harga_per_kg'])
                ->map(function (Sampah $row) {
                return [
                    'id_jenis' => $row->id_jenis_sampah,
                    'nama_jenis' => $row->nama_jenis ?? '-',
                    'harga_per_kg' => (int) round((float) ($row->harga_per_kg ?? 0)),
                ];
            })->all();
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch jenis sampah: ' . $e->getMessage());
            return [];
        }
    }

    private function fetchWasteTypeById(int $idJenis): ?array
    {
        try {
            $row = Sampah::where('id_jenis_sampah', $idJenis)
                ->where('status', 'aktif')
                ->first();
            if (!$row) {
                return null;
            }

            return [
                'id_jenis' => $row->id_jenis_sampah,
                'nama_jenis' => $row->nama_jenis ?? '-',
                'harga_per_kg' => (int) round((float) ($row->harga_per_kg ?? 0)),
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch selected jenis sampah: ' . $e->getMessage());
            return null;
        }
    }

    private function isValidImageDataUrl(string $photo): bool
    {
        return $this->decodeImageDataUrl($photo) !== null;
    }

    private function decodeImageDataUrl(string $photo): ?array
    {
        $photo = trim($photo);
        if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/i', $photo, $matches)) {
            return null;
        }

        if (strlen($photo) > 3500000) {
            return null;
        }

        $extension = strtolower($matches[1]) === 'png' ? 'png' : 'jpg';
        $base64 = preg_replace('/^data:image\/(jpeg|jpg|png);base64,/i', '', $photo);
        if (!is_string($base64) || $base64 === '') {
            return null;
        }

        $binary = base64_decode($base64, true);
        if ($binary === false || strlen($binary) === 0 || strlen($binary) > 2 * 1024 * 1024) {
            return null;
        }

        if (@getimagesizefromstring($binary) === false) {
            return null;
        }

        return [
            'bytes' => $binary,
            'extension' => $extension,
        ];
    }

    private function storeWastePhoto(string $photo, int $nasabahId, int $transaksiId, int $detailId, int $idJenis): string
    {
        $decoded = $this->decodeImageDataUrl($photo);
        if ($decoded === null) {
            throw new \RuntimeException('Foto sampah tidak valid.');
        }

        $path = sprintf(
            'setor-sampah/%d/%d_%d_%d_%s.%s',
            $nasabahId,
            $transaksiId,
            $detailId,
            $idJenis,
            bin2hex(random_bytes(6)),
            $decoded['extension']
        );

        if (! Storage::disk('public')->put($path, $decoded['bytes'])) {
            throw new \RuntimeException('Gagal menyimpan foto sampah.');
        }

        return 'storage/'.$path;
    }

    private function detectWastePhotoWithGroq(string $photo, string $expectedWasteType, array $wasteTypeNames): array
    {
        $apiKey = trim((string) config('services.groq.key', ''));
        if ($apiKey === '') {
            throw new \RuntimeException('GROQ_API_KEY belum dikonfigurasi.');
        }

        $model = (string) config('services.groq.vision_model', 'meta-llama/llama-4-scout-17b-16e-instruct');
        $endpoint = (string) config('services.groq.endpoint', 'https://api.groq.com/openai/v1/chat/completions');
        $allowedTypes = implode(', ', $wasteTypeNames);

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah validator foto setor sampah bank sampah. Jawab hanya JSON valid.',
                ],
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => "Periksa gambar ini.\n"
                                . "Jenis sampah yang dipilih nasabah: {$expectedWasteType}\n"
                                . "Daftar jenis sampah valid dari admin: {$allowedTypes}\n\n"
                                . "Aturan:\n"
                                . "1. Foto valid hanya jika objek utama adalah sampah atau material daur ulang nyata.\n"
                                . "2. Jika foto bukan sampah, terlalu buram, tidak cukup jelas, atau objek utamanya bukan material sampah, is_waste=false dan matches_expected=false.\n"
                                . "3. Jika foto berisi sampah tetapi jenisnya tidak cocok secara visual dengan jenis yang dipilih, matches_expected=false.\n"
                                . "4. Pilih selected_type hanya dari daftar jenis sampah valid.\n"
                                . "5. Jika ragu, tolak.\n\n"
                                . "Kembalikan JSON dengan key: is_waste boolean, selected_type string atau null, matches_expected boolean, confidence number 0 sampai 1, reason string singkat bahasa Indonesia.",
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => $photo,
                            ],
                        ],
                    ],
                ],
            ],
            'temperature' => 0,
            'max_tokens' => 350,
            'response_format' => [
                'type' => 'json_object',
            ],
        ];

        $response = Http::acceptJson()
            ->withToken($apiKey)
            ->timeout(45)
            ->post($endpoint, $payload);

        if (!$response->successful() && in_array($response->status(), [400, 422], true)) {
            unset($payload['response_format']);
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout(45)
                ->post($endpoint, $payload);
        }

        if (!$response->successful()) {
            throw new \RuntimeException('Groq vision error HTTP ' . $response->status() . ': ' . $response->body());
        }

        $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));
        $json = $this->decodeJsonObject($content);

        if (!$json) {
            throw new \RuntimeException('Hasil deteksi foto tidak valid.');
        }

        $isWaste = filter_var($json['is_waste'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        $matchesExpected = filter_var($json['matches_expected'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
        $detectedType = isset($json['selected_type']) && is_string($json['selected_type']) ? trim($json['selected_type']) : null;
        $confidence = isset($json['confidence']) && is_numeric($json['confidence']) ? (float) $json['confidence'] : ($matchesExpected ? 0.75 : 0);
        $confidence = max(0, min(1, $confidence));
        $reason = trim((string) ($json['reason'] ?? 'Foto tidak sesuai dengan jenis sampah yang dipilih.'));

        $valid = $isWaste && $matchesExpected && $confidence >= 0.45;
        $message = $valid
            ? 'Foto valid.'
            : $this->buildDetectionFailureMessage($isWaste, $matchesExpected, $expectedWasteType, $detectedType, $reason);

        return [
            'valid' => $valid,
            'detected_type' => $detectedType,
            'confidence' => $confidence,
            'message' => $message,
        ];
    }

    private function decodeJsonObject(string $content): ?array
    {
        $decoded = json_decode($content, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $content, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    private function buildDetectionFailureMessage(bool $isWaste, bool $matchesExpected, string $expectedWasteType, ?string $detectedType, string $reason): string
    {
        if (!$isWaste) {
            return 'Foto yang diupload tidak terdeteksi sebagai sampah. Silakan upload foto sampah yang jelas.';
        }

        if (!$matchesExpected) {
            $detected = $detectedType ? ' Terdeteksi: ' . $detectedType . '.' : '';
            return 'Foto sampah tidak sesuai dengan jenis "' . $expectedWasteType . '".' . $detected;
        }

        return mb_substr($reason !== '' ? $reason : 'Foto tidak sesuai dengan jenis sampah yang dipilih.', 0, 180);
    }

    private function rememberValidatedWastePhoto(string $photo, int $idJenis, string $namaJenis): void
    {
        if (!request()->hasSession()) {
            return;
        }

        $approvedPhotos = $this->pruneValidatedWastePhotos(session('validated_waste_photos', []));
        $approvedPhotos[hash('sha256', $photo)] = [
            'id_jenis' => $idJenis,
            'nama_jenis' => $namaJenis,
            'checked_at' => time(),
        ];

        session(['validated_waste_photos' => $approvedPhotos]);
    }

    private function pruneValidatedWastePhotos(array $approvedPhotos): array
    {
        $validUntil = time() - (2 * 60 * 60);

        return array_filter($approvedPhotos, function ($item) use ($validUntil) {
            return is_array($item) && (int) ($item['checked_at'] ?? 0) >= $validUntil;
        });
    }

    private function validateWasteItems(array $wasteItems, array $wastePhotos): ?string
    {
        $approvedPhotos = $this->pruneValidatedWastePhotos(session('validated_waste_photos', []));
        session(['validated_waste_photos' => $approvedPhotos]);

        foreach ($wasteItems as $index => $item) {
            $idJenis = isset($item['id_jenis']) ? intval($item['id_jenis']) : 0;
            $berat = isset($item['berat']) ? (float) $item['berat'] : 0;

            if ($idJenis <= 0) {
                return 'Jenis sampah tidak valid.';
            }

            if ($berat < 1) {
                return 'Berat minimal 1 kg untuk setiap item.';
            }

            if (!isset($wastePhotos[$index]) || trim((string) $wastePhotos[$index]) === '') {
                return 'Setiap item wajib memiliki foto.';
            }

            $photoHash = hash('sha256', trim((string) $wastePhotos[$index]));
            $approval = $approvedPhotos[$photoHash] ?? null;
            if (!is_array($approval) || (int) ($approval['id_jenis'] ?? 0) !== $idJenis) {
                return 'Foto item ke-' . ($index + 1) . ' belum lolos deteksi otomatis atau tidak sesuai dengan jenis sampah yang dipilih.';
            }
        }

        return null;
    }

    private function calculateTotalsFromDatabase(array $wasteItems): array
    {
        $ids = array_values(array_unique(array_map(function ($item) {
            return intval($item['id_jenis'] ?? 0);
        }, $wasteItems)));

        $ids = array_filter($ids, function ($id) {
            return $id > 0;
        });

        if (count($ids) === 0) {
            return ['error' => 'Jenis sampah tidak ditemukan.', 'total_nilai' => 0, 'detail_rows' => []];
        }

        $priceMap = [];
        foreach (Sampah::whereIn('id_jenis_sampah', $ids)->where('status', 'aktif')->get() as $row) {
            $priceMap[(int) $row->id_jenis_sampah] = (int) round((float) ($row->harga_per_kg ?? 0));
        }

        $totalNilai = 0;
        $detailRows = [];

        foreach ($wasteItems as $item) {
            $idJenis = intval($item['id_jenis'] ?? 0);
            $berat = (float) ($item['berat'] ?? 0);
            if (!isset($priceMap[$idJenis])) {
                return ['error' => 'Jenis sampah tidak ditemukan.', 'total_nilai' => 0, 'detail_rows' => []];
            }

            $harga = $priceMap[$idJenis];
            $subtotal = (int) round($harga * $berat);
            $totalNilai += $subtotal;

            $detailRows[] = [
                'id_jenis' => $idJenis,
                'berat_kg' => $berat,
                'harga_kg' => $harga,
                'subtotal' => $subtotal,
                'status_item' => 'pending',
            ];
        }

        return ['error' => null, 'total_nilai' => $totalNilai, 'detail_rows' => $detailRows];
    }
}
