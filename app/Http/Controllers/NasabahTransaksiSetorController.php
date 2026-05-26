<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NasabahTransaksiSetorController extends Controller
{
    public function index(Request $request)
    {
        $activePage = 'setor';

        $user = null;
        $id = session('id_nasabah');

        // Try to load real user profile from Supabase when logged in
        if ($id) {
            try {
                $supabaseUrl = env('SUPABASE_URL');
                $supabaseKey = env('SUPABASE_KEY');

                $resp = Http::withHeaders([
                    'apikey' => $supabaseKey,
                    'Authorization' => 'Bearer ' . $supabaseKey,
                ])->get($supabaseUrl . '/rest/v1/nasabah?select=*&id_nasabah=eq.' . intval($id));

                $data = $resp->json();
                if (is_array($data) && count($data) > 0) {
                    $row = $data[0];
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
                \Log::warning('Failed to fetch nasabah for setor: ' . $e->getMessage());
            }
        }

        // Fallback to session or dummy if still null
        if (!$user) {
            $user = [
                'id_nasabah' => session('id_nasabah') ?? 1,
                'nama_nasabah' => session('nama_nasabah') ?? 'Ridho Pratama',
                'alamat' => session('alamat') ?? 'Jl. Merdeka No. 42, Jakarta Selatan',
                'saldo' => session('saldo') ?? 250000,
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
                        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY');
                        $supabaseUrl = env('SUPABASE_URL');

                        $calcResult = $this->calculateTotalsFromSupabase($waste_items, $serviceKey, $supabaseUrl);
                        if ($calcResult['error']) {
                            $error = $calcResult['error'];
                        } else {
                            $totalNilai = $calcResult['total_nilai'];
                            $detailRows = $calcResult['detail_rows'];

                            $transaksiPayload = [
                                'id_nasabah' => intval($user['id_nasabah']),
                                'total_nilai' => $totalNilai,
                                'tanggal_setor' => date('Y-m-d'),
                                'status' => 'menunggu',
                            ];

                            $transaksiResp = Http::withHeaders([
                                'apikey' => $serviceKey,
                                'Authorization' => 'Bearer ' . $serviceKey,
                                'Content-Type' => 'application/json',
                                'Prefer' => 'return=representation',
                            ])->post($supabaseUrl . '/rest/v1/transaksi_setor', $transaksiPayload);

                            if (!$transaksiResp->successful()) {
                                $error = 'Gagal mengajukan transaksi setor (HTTP ' . $transaksiResp->status() . ').';
                            } else {
                                $transaksiData = $transaksiResp->json();
                                $transaksiId = is_array($transaksiData) && count($transaksiData) > 0
                                    ? ($transaksiData[0]['id_transaksi_setor'] ?? null)
                                    : null;

                                if (!$transaksiId) {
                                    $error = 'Transaksi dibuat tetapi ID tidak ditemukan.';
                                } else {
                                    foreach ($detailRows as &$row) {
                                        $row['id_transaksi_setor'] = intval($transaksiId);
                                    }
                                    unset($row);

                                    $detailResp = Http::withHeaders([
                                        'apikey' => $serviceKey,
                                        'Authorization' => 'Bearer ' . $serviceKey,
                                        'Content-Type' => 'application/json',
                                        'Prefer' => 'return=representation',
                                    ])->post($supabaseUrl . '/rest/v1/detail_setor', $detailRows);

                                    if (!$detailResp->successful()) {
                                        $error = 'Transaksi dibuat, tetapi detail setor gagal disimpan.';
                                    } else {
                                        $fotoRows = [];
                                        foreach ($waste_items as $index => $item) {
                                            $foto = $waste_photos[$index] ?? null;
                                            if ($foto) {
                                                $fotoRows[] = [
                                                    'id_transaksi_setor' => intval($transaksiId),
                                                    'foto_url' => $foto,
                                                ];
                                            }
                                        }

                                        if (count($fotoRows) > 0) {
                                            $fotoResp = Http::withHeaders([
                                                'apikey' => $serviceKey,
                                                'Authorization' => 'Bearer ' . $serviceKey,
                                                'Content-Type' => 'application/json',
                                                'Prefer' => 'return=representation',
                                            ])->post($supabaseUrl . '/rest/v1/foto_setor', $fotoRows);

                                            if (!$fotoResp->successful()) {
                                                $error = 'Transaksi dibuat, tetapi foto gagal disimpan.';
                                            }
                                        }

                                        if (!$error) {
                                            session()->forget('validated_waste_photos');
                                            $success = 'Transaksi setor sampah berhasil diajukan! Status: Menunggu persetujuan admin.';
                                        }
                                    }
                                }
                            }
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
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');
        if (!$supabaseUrl || !$supabaseKey) {
            return [];
        }

        try {
            $resp = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/jenis_sampah?select=id_jenis_sampah,nama_jenis,harga_per_kg,status&status=eq.aktif&order=nama_jenis.asc');

            $rows = $resp->json();
            if (!is_array($rows)) {
                return [];
            }

            return array_map(function ($row) {
                return [
                    'id_jenis' => $row['id_jenis_sampah'] ?? null,
                    'nama_jenis' => $row['nama_jenis'] ?? '-',
                    'harga_per_kg' => (int) round((float) ($row['harga_per_kg'] ?? 0)),
                ];
            }, $rows);
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch jenis sampah: ' . $e->getMessage());
            return [];
        }
    }

    private function fetchWasteTypeById(int $idJenis): ?array
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');
        if (!$supabaseUrl || !$supabaseKey) {
            return null;
        }

        try {
            $resp = Http::withHeaders([
                'apikey' => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
            ])->get($supabaseUrl . '/rest/v1/jenis_sampah?select=id_jenis_sampah,nama_jenis,harga_per_kg,status&id_jenis_sampah=eq.' . $idJenis . '&status=eq.aktif&limit=1');

            if (!$resp->successful()) {
                return null;
            }

            $rows = $resp->json();
            if (!is_array($rows) || count($rows) === 0) {
                return null;
            }

            $row = $rows[0];

            return [
                'id_jenis' => $row['id_jenis_sampah'] ?? null,
                'nama_jenis' => $row['nama_jenis'] ?? '-',
                'harga_per_kg' => (int) round((float) ($row['harga_per_kg'] ?? 0)),
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to fetch selected jenis sampah: ' . $e->getMessage());
            return null;
        }
    }

    private function isValidImageDataUrl(string $photo): bool
    {
        if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,/i', $photo)) {
            return false;
        }

        if (strlen($photo) > 3500000) {
            return false;
        }

        $base64 = preg_replace('/^data:image\/(jpeg|jpg|png);base64,/i', '', $photo);
        if (!is_string($base64) || $base64 === '') {
            return false;
        }

        $binary = base64_decode($base64, true);
        if ($binary === false || strlen($binary) > 2 * 1024 * 1024) {
            return false;
        }

        return @getimagesizefromstring($binary) !== false;
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

    private function calculateTotalsFromSupabase(array $wasteItems, string $serviceKey, string $supabaseUrl): array
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

        $idFilter = implode(',', $ids);
        $resp = Http::withHeaders([
            'apikey' => $serviceKey,
            'Authorization' => 'Bearer ' . $serviceKey,
        ])->get($supabaseUrl . '/rest/v1/jenis_sampah?select=id_jenis_sampah,harga_per_kg,status&id_jenis_sampah=in.(' . $idFilter . ')&status=eq.aktif');

        if (!$resp->successful()) {
            return ['error' => 'Gagal memuat harga jenis sampah.', 'total_nilai' => 0, 'detail_rows' => []];
        }

        $rows = $resp->json();
        if (!is_array($rows)) {
            return ['error' => 'Data jenis sampah tidak valid.', 'total_nilai' => 0, 'detail_rows' => []];
        }

        $priceMap = [];
        foreach ($rows as $row) {
            $id = $row['id_jenis_sampah'] ?? null;
            if ($id !== null) {
                $priceMap[intval($id)] = (int) round((float) ($row['harga_per_kg'] ?? 0));
            }
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
