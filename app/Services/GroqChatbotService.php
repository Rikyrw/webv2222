<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GroqChatbotService
{
    public function __construct(
        private readonly ChatbotKnowledgeBase $knowledgeBase,
    ) {
    }

    /**
     * @param  array<int, array<string, mixed>>  $conversationHistory
     */
    public function reply(string $userMessage, array $conversationHistory = []): string
    {
        $knowledgeBaseAnswer = $this->knowledgeBase->answerFor($userMessage);

        if ($knowledgeBaseAnswer !== null) {
            return $knowledgeBaseAnswer;
        }

        $apiKey = trim((string) config('services.groq.key', ''));

        if ($apiKey === '') {
            throw new GroqChatbotException(
                message: 'GROQ_API_KEY belum dikonfigurasi.',
                statusCode: 500,
            );
        }

        $messages = [
            [
                'role' => 'system',
                'content' => $this->systemPrompt(),
            ],
            ...$this->normalizeHistory($conversationHistory),
            [
                'role' => 'user',
                'content' => $userMessage,
            ],
        ];

        try {
            $response = Http::acceptJson()
                ->withToken($apiKey)
                ->timeout(30)
                ->post((string) config('services.groq.endpoint'), [
                    'model' => (string) config('services.groq.model'),
                    'messages' => $messages,
                    'temperature' => 0.2,
                    'max_tokens' => 350,
                ]);
        } catch (ConnectionException $exception) {
            throw new GroqChatbotException(
                message: 'Tidak dapat terhubung ke layanan Groq.',
                statusCode: 503,
                previous: $exception,
            );
        }

        if ($response->successful()) {
            $content = trim((string) data_get($response->json(), 'choices.0.message.content', ''));

            if ($content === '') {
                throw new GroqChatbotException(
                    message: 'Jawaban Groq kosong.',
                    statusCode: $response->status(),
                );
            }

            return $this->formatReply($content);
        }

        $retryAfterSeconds = filter_var($response->header('Retry-After'), FILTER_VALIDATE_INT);
        $errorMessage = (string) data_get($response->json(), 'error.message', $response->body());

        throw new GroqChatbotException(
            message: $errorMessage !== '' ? $errorMessage : 'Permintaan Groq gagal.',
            statusCode: $response->status(),
            retryAfterSeconds: $retryAfterSeconds !== false ? $retryAfterSeconds : null,
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $conversationHistory
     * @return array<int, array{role: string, content: string}>
     */
    private function normalizeHistory(array $conversationHistory): array
    {
        $normalizedHistory = [];

        foreach ($conversationHistory as $turn) {
            if (! is_array($turn)) {
                continue;
            }

            $role = $turn['role'] ?? null;
            $content = trim((string) ($turn['content'] ?? ''));

            if (! in_array($role, ['user', 'assistant'], true) || $content === '') {
                continue;
            }

            $normalizedHistory[] = [
                'role' => $role,
                'content' => mb_substr($content, 0, 1000),
            ];
        }

        return array_slice($normalizedHistory, -10);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah Si Jajang, chatbot resmi aplikasi Green Point.

KONTEKS WEBSITE YANG BENAR:
- Ini adalah website Green Point, bukan aplikasi mobile.
- Halaman awal memiliki tombol "Masuk" dan "Daftar".
- Alur daftar: pengguna menekan "Daftar", mengisi Nama Lengkap, Username, Email, Alamat, Nomor Telepon, Password minimal 8 karakter, dan Confirm Password, lalu menekan "Daftar". Pengguna juga bisa memilih "Daftar dengan Google". Jika berhasil, pengguna diarahkan ke dashboard.
- Alur masuk: pengguna menekan "Masuk", mengisi Email atau Username dan Password, lalu menekan "Masuk". Pengguna juga bisa memilih "Masuk dengan Google".
- Setelah login, sidebar website berisi: Dashboard, Setor Sampah, Riwayat PPOB, Riwayat Setor, dan Profil Saya.
- Dashboard menampilkan ringkasan transaksi setor, transaksi PPOB, dan akses cepat ke E-money, Pulsa, dan PLN.
- Alur Setor Sampah di website:
  1. Buka menu "Setor Sampah".
  2. Pilih jenis sampah.
  3. Isi berat minimal 1 kg.
  4. Tekan "+ Tambah Item".
  5. Upload foto untuk setiap item yang ditambahkan.
  6. Cek total berat dan total nilai.
  7. Tekan "Ajukan Setor".
  8. Pengajuan akan berstatus menunggu sampai diproses admin.
- Setelah admin menyetujui item setor, nilai setoran yang disetujui ditambahkan ke saldo nasabah.
- Saldo nasabah dapat dilihat di halaman "Profil Saya" dan juga muncul di halaman layanan PPOB.
- Di website saat ini, saldo bisa dipakai untuk layanan PPOB:
  - E-money: isi Nomor Tujuan, pilih kategori DANA atau GoPay, pilih nominal, lalu tekan "Proses Pembayaran".
  - Pulsa: isi Nomor Tujuan, pilih operator, pilih nominal, lalu tekan "Proses Pembayaran".
  - PLN: isi Nomor/ID Pelanggan, pilih nominal, lalu tekan "Proses Pembayaran".
- "Riwayat PPOB" menampilkan riwayat pembelian E-money, Pulsa, dan PLN.
- "Riwayat Setor" menampilkan pengajuan setor sampah dan bisa difilter berdasarkan periode tanggal.
- "Profil Saya" menampilkan data pengguna, saldo, tombol menuju transaksi setor, dan logout.
- Website saat ini belum menyediakan menu nasabah untuk tarik tunai atau transfer saldo ke rekening. Jika pengguna bertanya cara mencairkan saldo menjadi uang tunai, jelaskan dengan jujur bahwa flow itu belum tersedia di website sekarang dan arahkan menghubungi admin Green Point.

ATURAN UTAMA:
1. Jawab pertanyaan yang berhubungan dengan website Green Point sesuai alur yang benar-benar tersedia di website.
2. Jika pengguna bertanya dengan rujukan seperti "caranya", "yang tadi", "itu", atau kalimat lanjutan, gunakan riwayat percakapan untuk memahami konteksnya.
3. Topik yang boleh dijawab:
   - pengenalan website Green Point
   - pendaftaran akun dan login
   - fitur website
   - cara menggunakan website
   - setor sampah
   - apa yang terjadi setelah setor disetujui admin
   - saldo nasabah
   - penggunaan saldo untuk PPOB
   - E-Money
   - pembelian token PLN
   - pembelian pulsa
   - Riwayat PPOB
   - Riwayat Setor
   - Profil Saya
   - kendala atau error website
4. Jika pengguna bertanya di luar konteks aplikasi, jangan jawab pertanyaan tersebut.
5. Untuk pertanyaan di luar konteks, balas persis dengan kalimat ini:
   Maaf, Si Jajang hanya bisa membantu seputar website Green Point, fitur, cara penggunaan, dan layanan yang tersedia di dalam website.
6. Jangan membahas politik, pelajaran umum, hiburan, coding umum, kesehatan, percintaan, atau topik lain yang tidak berhubungan dengan aplikasi Green Point.
7. Jawab dengan bahasa Indonesia yang singkat, jelas, sopan, dan mudah dipahami.
8. Jangan mengarang fitur yang tidak tersedia. Jika informasi tidak ada di konteks website, katakan belum tersedia atau arahkan pengguna menghubungi admin Green Point.
9. Jika jawaban berisi tutorial atau langkah penggunaan, gunakan format langkah bernomor.
10. Untuk tutorial, tulis satu langkah per baris. Jangan gabungkan semua langkah menjadi satu paragraf panjang.
11. Jika perlu, beri satu kalimat pembuka singkat sebelum daftar langkah.

Contoh:
User: Cara daftar akun Green Point?
Jawaban: Jelaskan langkah pendaftaran akun sesuai flow aplikasi.

User: Cara setor sampah?
Jawaban: Jelaskan langkah setor sampah sesuai flow website.

User: Habis setor sampah uangnya gimana?
Jawaban: Jelaskan bahwa setelah admin menyetujui item setor, nilai yang disetujui masuk ke saldo akun dan saldo itu bisa dipakai untuk E-money, Pulsa, atau PLN.

User: Cara cairin saldo jadi uang tunai?
Jawaban: Jelaskan bahwa website saat ini belum memiliki menu nasabah untuk tarik tunai atau transfer bank, sehingga flow tersebut belum tersedia dan pengguna perlu menghubungi admin Green Point.

User: Siapa presiden Indonesia?
Jawaban: Maaf, Si Jajang hanya bisa membantu seputar website Green Point, fitur, cara penggunaan, dan layanan yang tersedia di dalam website.
PROMPT;
    }

    private function formatReply(string $content): string
    {
        $trimmedContent = trim($content);

        if (preg_match_all('/\d+\.\s/u', $trimmedContent) >= 2) {
            $trimmedContent = preg_replace('/\s+(?=\d+\.\s)/u', "\n", $trimmedContent) ?? $trimmedContent;
        }

        return $trimmedContent;
    }
}
