<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\TopupSaldo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NasabahTopupController extends Controller
{
    public function create(Request $request)
    {
        $authenticatedNasabah = $request->user() instanceof Nasabah ? $request->user() : null;
        $userId = $authenticatedNasabah?->id_nasabah ?: session('id_nasabah') ?: $request->input('id_nasabah');
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

        $midtransServerKey = env('MIDTRANS_SERVER_KEY');
        $midtransClientKey = env('MIDTRANS_CLIENT_KEY');
        $isProd = filter_var(env('MIDTRANS_IS_PROD', false), FILTER_VALIDATE_BOOL);

        if (!$midtransServerKey || !$midtransClientKey) {
            return response()->json(['message' => 'Midtrans belum dikonfigurasi.'], 500);
        }

        $nominal = (int) $request->input('nominal');
        $orderId = 'TOPUP-'.$userId.'-'.date('YmdHis').'-'.Str::random(6);

        try {
            $topup = TopupSaldo::create([
                'id_nasabah' => $userId,
                'order_id' => $orderId,
                'gross_amount' => $nominal,
                'status' => 'pending',
                'transaction_status' => 'pending',
                'payment_type' => null,
            ]);
        } catch (\Throwable $exception) {
            Log::error('Topup insert failed: '.$exception->getMessage());

            return response()->json(['message' => 'Gagal menyimpan transaksi top up.'], 500);
        }

        $snapUrl = $isProd
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $customerDetails = [
            'first_name' => $authenticatedNasabah?->nama_lengkap ?: session('nama_nasabah') ?: ($request->input('nama_lengkap') ?: 'Nasabah'),
            'email' => $authenticatedNasabah?->email ?: session('email') ?: ($request->input('email') ?: 'unknown@example.com'),
            'phone' => $authenticatedNasabah?->no_hp ?: session('no_hp') ?: ($request->input('no_hp') ?: ''),
        ];

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $nominal,
            ],
            'customer_details' => $customerDetails,
            'item_details' => [[
                'id' => 'TOPUP',
                'price' => $nominal,
                'quantity' => 1,
                'name' => 'Top Up Saldo GreenPoint',
            ]],
        ];

        try {
            $snapResp = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($midtransServerKey.':'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($snapUrl, $payload);

            if (!$snapResp->successful()) {
                Log::error('Midtrans snap error: '.$snapResp->body());
                $topup->update([
                    'status' => 'failed',
                    'transaction_status' => 'failed',
                ]);

                return response()->json(['message' => 'Gagal membuat token pembayaran.'], 500);
            }

            $snapData = $snapResp->json();
            $topup->update([
                'snap_token' => $snapData['token'] ?? null,
                'snap_redirect_url' => $snapData['redirect_url'] ?? null,
            ]);

            return response()->json([
                'token' => $snapData['token'] ?? null,
                'order_id' => $orderId,
                'redirect_url' => $snapData['redirect_url'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans request error: '.$e->getMessage());

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

        $expectedSignature = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);
        if (!hash_equals($expectedSignature, $signatureKey)) {
            return response()->json(['message' => 'Invalid signature.'], 401);
        }

        $topup = TopupSaldo::where('order_id', $orderId)->first();
        if (!$topup) {
            Log::warning('Midtrans notification: top up not found.', ['order_id' => $orderId]);

            return response()->json(['message' => 'OK']);
        }

        $this->applyMidtransPayloadToTopup($topup, $payload);

        return response()->json(['message' => 'OK']);
    }

    public function checkStatus(Request $request)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return response()->json(['message' => 'order_id required.'], 422);
        }

        $topup = TopupSaldo::where('order_id', $orderId)->first();
        if (!$topup) {
            return response()->json(['status' => 'not_found']);
        }

        $authenticatedNasabah = $request->user() instanceof Nasabah ? $request->user() : null;
        if ($authenticatedNasabah && (int) $topup->id_nasabah !== (int) $authenticatedNasabah->id_nasabah) {
            return response()->json(['status' => 'not_found']);
        }

        $topup = $this->syncTopupFromMidtrans($topup);
        $status = $topup->status ?? 'pending';
        $transactionStatus = $topup->transaction_status ?? $status;

        $response = [
            'status' => $status,
            'transaction_status' => $transactionStatus,
        ];

        if ($status === 'settlement') {
            $saldo = $this->fetchNasabahSaldo((int) $topup->id_nasabah);
            if ($saldo !== null) {
                $response['saldo'] = $saldo;
                session(['saldo' => $saldo]);
            }
        }

        return response()->json($response);
    }

    private function syncTopupFromMidtrans(TopupSaldo $topup): TopupSaldo
    {
        $status = strtolower(trim((string) ($topup->status ?? 'pending')));
        $transactionStatus = strtolower(trim((string) ($topup->transaction_status ?? $status)));

        if (in_array($status, ['settlement', 'expire', 'cancel', 'deny', 'failure', 'failed'], true)) {
            return $topup;
        }

        if ($transactionStatus === 'settlement') {
            return $this->applyMidtransPayloadToTopup($topup, [
                'order_id' => $topup->order_id,
                'transaction_status' => $transactionStatus,
                'payment_type' => $topup->payment_type,
                'gross_amount' => $topup->gross_amount,
            ]);
        }

        $midtransPayload = $this->fetchMidtransStatus($topup->order_id);
        if (!$midtransPayload) {
            return $topup;
        }

        return $this->applyMidtransPayloadToTopup($topup, $midtransPayload);
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
                'Authorization' => 'Basic '.base64_encode($serverKey.':'),
                'Accept' => 'application/json',
            ])->get($baseUrl.rawurlencode($orderId).'/status');

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
            Log::warning('Midtrans status check error: '.$e->getMessage(), [
                'order_id' => $orderId,
            ]);

            return null;
        }
    }

    private function applyMidtransPayloadToTopup(TopupSaldo $topup, array $payload): TopupSaldo
    {
        $oldStatus = strtolower(trim((string) $topup->status));
        $transactionStatus = strtolower(trim((string) ($payload['transaction_status'] ?? ($topup->transaction_status ?? 'pending'))));
        $paymentType = $payload['payment_type'] ?? $topup->payment_type;
        $fraudStatus = strtolower(trim((string) ($payload['fraud_status'] ?? '')));
        $isSuccess = in_array($transactionStatus, ['settlement', 'capture'], true) && $fraudStatus !== 'challenge';
        $newStatus = $isSuccess ? 'settlement' : ($fraudStatus === 'challenge' ? 'challenge' : $transactionStatus);

        if ($oldStatus !== 'settlement') {
            $topup->update([
                'status' => $newStatus,
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'transaction_id' => $payload['transaction_id'] ?? $topup->transaction_id,
                'fraud_status' => $payload['fraud_status'] ?? $topup->fraud_status,
                'transaction_time' => $payload['transaction_time'] ?? $topup->transaction_time,
                'raw_notification' => $payload,
            ]);

            if ($isSuccess) {
                $this->creditTopupBalance($topup, (float) ($topup->gross_amount ?: ($payload['gross_amount'] ?? 0)));
            }
        }

        return $topup->fresh() ?? $topup;
    }

    private function fetchNasabahSaldo(int $nasabahId): ?float
    {
        $saldo = Nasabah::where('id_nasabah', $nasabahId)->value('saldo');

        return $saldo === null ? null : (float) $saldo;
    }

    private function creditTopupBalance(TopupSaldo $topup, float $grossAmount): ?float
    {
        if ($grossAmount <= 0) {
            return null;
        }

        $nasabah = Nasabah::where('id_nasabah', $topup->id_nasabah)->first();
        if (!$nasabah) {
            return null;
        }

        $newSaldo = (float) $nasabah->saldo + $grossAmount;
        $nasabah->update(['saldo' => $newSaldo]);

        if ((int) session('id_nasabah') === (int) $nasabah->id_nasabah) {
            session(['saldo' => $newSaldo]);
        }

        return $newSaldo;
    }
}
