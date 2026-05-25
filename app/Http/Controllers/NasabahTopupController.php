<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NasabahTopupController extends Controller
{
    public function create(Request $request)
    {
        $userId = session('id_nasabah') ?: $request->input('id_nasabah');
        if (!$userId || !is_numeric($userId)) {
            return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }
        $userId = (int) $userId;

        $validator = Validator::make($request->all(), [
            'nominal' => 'required|integer|min:10000|max:10000000',
        ], [
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.integer' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Minimal top up Rp 10.000.',
            'nominal.max' => 'Nominal terlalu besar.',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        $nominal = (int) $request->input('nominal');
        $orderId = 'TOPUP-' . $userId . '-' . date('YmdHis') . '-' . Str::random(6);

        $midtransServerKey = env('MIDTRANS_SERVER_KEY');
        $midtransClientKey = env('MIDTRANS_CLIENT_KEY');
        $isProd = filter_var(env('MIDTRANS_IS_PROD', false), FILTER_VALIDATE_BOOL);

        if (!$midtransServerKey || !$midtransClientKey) {
            return response()->json(['message' => 'Midtrans belum dikonfigurasi.'], 500);
        }

        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = env('SUPABASE_KEY');
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY') ?: $supabaseKey;

        if (!$supabaseUrl || !$serviceKey) {
            return response()->json(['message' => 'Supabase belum dikonfigurasi.'], 500);
        }

        $topupPayload = [
            'id_nasabah' => $userId,
            'order_id' => $orderId,
            'gross_amount' => $nominal,
            'status' => 'pending',
            'transaction_status' => 'pending',
            'payment_type' => null,
        ];

        $insertResp = $this->supabaseRequest('post', '/rest/v1/topup_saldo', $topupPayload, true, true);
        if (!$insertResp->successful()) {
            Log::error('Topup insert failed: ' . $insertResp->body());
            return response()->json(['message' => 'Gagal menyimpan transaksi top up.'], 500);
        }

        $snapUrl = $isProd
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $customerDetails = [
            'first_name' => session('nama_nasabah') ?: ($request->input('nama_lengkap') ?: 'Nasabah'),
            'email' => session('email') ?: ($request->input('email') ?: 'unknown@example.com'),
            'phone' => session('no_hp') ?: ($request->input('no_hp') ?: ''),
        ];

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $nominal,
            ],
            'customer_details' => $customerDetails,
            'item_details' => [
                [
                    'id' => 'TOPUP',
                    'price' => $nominal,
                    'quantity' => 1,
                    'name' => 'Top Up Saldo GreenPoint',
                ]
            ],
        ];

        $authHeader = 'Basic ' . base64_encode($midtransServerKey . ':');

        try {
            $snapResp = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($snapUrl, $payload);

            if (!$snapResp->successful()) {
                Log::error('Midtrans snap error: ' . $snapResp->body());
                $this->supabaseRequest('patch', '/rest/v1/topup_saldo?order_id=eq.' . urlencode($orderId), [
                    'status' => 'failed',
                    'transaction_status' => 'failed',
                ], false, true);

                return response()->json(['message' => 'Gagal membuat token pembayaran.'], 500);
            }

            $snapData = $snapResp->json();
            $snapToken = $snapData['token'] ?? null;

            if ($snapToken) {
                $this->supabaseRequest('patch', '/rest/v1/topup_saldo?order_id=eq.' . urlencode($orderId), [
                    'snap_token' => $snapToken,
                ], false, true);
            }

            return response()->json([
                'token' => $snapToken,
                'order_id' => $orderId,
                'redirect_url' => $snapData['redirect_url'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans request error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan saat memproses pembayaran.'], 500);
        }
    }

    public function handleNotification(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload.'], 400);
        }

        $serverKey = env('MIDTRANS_SERVER_KEY');
        if (!$serverKey) {
            return response()->json(['message' => 'Server key tidak tersedia.'], 500);
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if (!hash_equals($expectedSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $transactionStatus = $payload['transaction_status'] ?? 'pending';
        $paymentType = $payload['payment_type'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        $topupResp = $this->supabaseRequest(
            'get',
            '/rest/v1/topup_saldo?select=id_topup,id_nasabah,status,transaction_status,gross_amount,payment_type&order_id=eq.' . urlencode($orderId) . '&limit=1',
            null,
            false,
            true
        );

        if (!$topupResp->successful()) {
            Log::warning('Midtrans notification: top up lookup failed.', [
                'order_id' => $orderId,
                'response' => $topupResp->body(),
            ]);
            return response()->json(['message' => 'OK']);
        }

        $rows = $topupResp->json();
        if (!is_array($rows) || count($rows) === 0) {
            Log::warning('Midtrans notification: top up not found.', [
                'order_id' => $orderId,
            ]);
            return response()->json(['message' => 'OK']);
        }

        $topup = $rows[0];
        $this->applyMidtransPayloadToTopup($topup, $orderId, $payload);

        return response()->json(['message' => 'OK']);
    }

    public function checkStatus(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return response()->json(['message' => 'order_id required.'], 422);
        }

        $topupResp = $this->supabaseRequest(
            'get',
            '/rest/v1/topup_saldo?select=id_topup,id_nasabah,status,transaction_status,gross_amount,payment_type&order_id=eq.' . urlencode($orderId) . '&limit=1',
            null,
            false,
            true
        );

        if (!$topupResp->successful()) {
            return response()->json(['status' => 'not_found']);
        }

        $rows = $topupResp->json();
        if (!is_array($rows) || count($rows) === 0) {
            return response()->json(['status' => 'not_found']);
        }

        $topup = $this->syncTopupFromMidtrans($rows[0], $orderId);
        $status = $topup['status'] ?? 'pending';
        $transactionStatus = $topup['transaction_status'] ?? $status;

        $response = [
            'status' => $status,
            'transaction_status' => $transactionStatus,
        ];

        if ($status === 'settlement') {
            $nasabahId = $topup['id_nasabah'] ?? null;
            if ($nasabahId) {
                $saldo = $this->fetchNasabahSaldo((int) $nasabahId);
                if ($saldo !== null) {
                    $response['saldo'] = $saldo;
                    session(['saldo' => $saldo]);
                }
            }
        }

        return response()->json($response);
    }

    private function syncTopupFromMidtrans(array $topup, string $orderId): array
    {
        $status = strtolower(trim((string) ($topup['status'] ?? 'pending')));
        $transactionStatus = strtolower(trim((string) ($topup['transaction_status'] ?? $status)));

        if (in_array($status, ['settlement', 'expire', 'cancel', 'deny', 'failure', 'failed'], true)) {
            return $topup;
        }

        if ($transactionStatus === 'settlement') {
            return $this->applyMidtransPayloadToTopup($topup, $orderId, [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_type' => $topup['payment_type'] ?? null,
                'gross_amount' => $topup['gross_amount'] ?? 0,
            ]);
        }

        $midtransPayload = $this->fetchMidtransStatus($orderId);
        if (!$midtransPayload) {
            return $topup;
        }

        return $this->applyMidtransPayloadToTopup($topup, $orderId, $midtransPayload);
    }

    private function fetchMidtransStatus(string $orderId): ?array
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        if (!$serverKey) {
            return null;
        }

        $isProd = filter_var(env('MIDTRANS_IS_PROD', false), FILTER_VALIDATE_BOOL);
        $baseUrl = $isProd
            ? 'https://api.midtrans.com/v2/'
            : 'https://api.sandbox.midtrans.com/v2/';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($serverKey . ':'),
                'Accept' => 'application/json',
            ])->get($baseUrl . rawurlencode($orderId) . '/status');

            if (!$response->successful()) {
                Log::warning('Midtrans status check failed.', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $payload = $response->json();
            return is_array($payload) ? $payload : null;
        } catch (\Exception $e) {
            Log::warning('Midtrans status check error: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
            return null;
        }
    }

    private function applyMidtransPayloadToTopup(array $topup, string $orderId, array $payload): array
    {
        $transactionStatus = strtolower(trim((string) ($payload['transaction_status'] ?? ($topup['transaction_status'] ?? 'pending'))));
        $paymentType = $payload['payment_type'] ?? ($topup['payment_type'] ?? null);
        $fraudStatus = strtolower(trim((string) ($payload['fraud_status'] ?? '')));
        $isSuccess = in_array($transactionStatus, ['settlement', 'capture'], true) && $fraudStatus !== 'challenge';
        $newStatus = $isSuccess ? 'settlement' : ($fraudStatus === 'challenge' ? 'challenge' : $transactionStatus);

        $updatePayload = [
            'status' => $newStatus,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'raw_notification' => json_encode($payload),
        ];

        $updateResp = $this->supabaseRequest(
            'patch',
            '/rest/v1/topup_saldo?order_id=eq.' . urlencode($orderId) . '&status=neq.settlement',
            $updatePayload,
            true,
            true
        );

        if (!$updateResp->successful()) {
            Log::warning('Topup status update failed.', [
                'order_id' => $orderId,
                'status' => $updateResp->status(),
                'body' => $updateResp->body(),
            ]);
            return $topup;
        }

        $updatedRows = $updateResp->json();
        if (!is_array($updatedRows) || count($updatedRows) === 0) {
            return $this->fetchTopupByOrderId($orderId) ?: array_merge($topup, $updatePayload);
        }

        $updatedTopup = array_merge($topup, $updatedRows[0]);

        if ($isSuccess) {
            $grossAmount = isset($updatedTopup['gross_amount'])
                ? (float) $updatedTopup['gross_amount']
                : (float) ($payload['gross_amount'] ?? 0);
            $saldo = $this->creditTopupBalance($updatedTopup, $grossAmount);
            if ($saldo !== null) {
                $updatedTopup['saldo'] = $saldo;
            }
        }

        return $updatedTopup;
    }

    private function fetchTopupByOrderId(string $orderId): ?array
    {
        $response = $this->supabaseRequest(
            'get',
            '/rest/v1/topup_saldo?select=id_topup,id_nasabah,status,transaction_status,gross_amount,payment_type&order_id=eq.' . urlencode($orderId) . '&limit=1',
            null,
            false,
            true
        );

        if (!$response->successful()) {
            return null;
        }

        $rows = $response->json();
        return is_array($rows) && count($rows) > 0 ? $rows[0] : null;
    }

    private function fetchNasabahSaldo(int $nasabahId): ?float
    {
        $nasabahResp = $this->supabaseRequest(
            'get',
            '/rest/v1/nasabah?select=saldo&id_nasabah=eq.' . $nasabahId . '&limit=1',
            null,
            false,
            true
        );

        if (!$nasabahResp->successful()) {
            return null;
        }

        $nasabahRows = $nasabahResp->json();
        if (!is_array($nasabahRows) || count($nasabahRows) === 0) {
            return null;
        }

        return (float) ($nasabahRows[0]['saldo'] ?? 0);
    }

    private function creditTopupBalance(array $topup, float $grossAmount): ?float
    {
        $nasabahId = $topup['id_nasabah'] ?? null;
        if (!$nasabahId || $grossAmount <= 0) {
            return null;
        }

        $saldo = $this->fetchNasabahSaldo((int) $nasabahId);
        if ($saldo === null) {
            return null;
        }

        $newSaldo = $saldo + $grossAmount;
        $saldoResp = $this->supabaseRequest(
            'patch',
            '/rest/v1/nasabah?id_nasabah=eq.' . intval($nasabahId),
            ['saldo' => $newSaldo],
            true,
            true
        );

        if (!$saldoResp->successful()) {
            Log::warning('Topup balance update failed.', [
                'id_nasabah' => $nasabahId,
                'status' => $saldoResp->status(),
                'body' => $saldoResp->body(),
            ]);
            return null;
        }

        if (intval(session('id_nasabah')) === intval($nasabahId)) {
            session(['saldo' => $newSaldo]);
        }

        return $newSaldo;
    }

    private function supabaseRequest(string $method, string $path, ?array $payload, bool $returnRepresentation, bool $useServiceRole)
    {
        $supabaseUrl = env('SUPABASE_URL');
        $supabaseKey = $useServiceRole
            ? (env('SUPABASE_SERVICE_ROLE_KEY') ?: env('SUPABASE_KEY'))
            : env('SUPABASE_KEY');

        $request = Http::withHeaders([
            'apikey' => $supabaseKey,
            'Authorization' => 'Bearer ' . $supabaseKey,
        ]);

        if ($returnRepresentation) {
            $request = $request->withHeaders([
                'Prefer' => 'return=representation',
            ]);
        }

        $url = rtrim((string) $supabaseUrl, '/') . $path;

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
