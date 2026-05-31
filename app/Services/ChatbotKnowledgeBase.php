<?php

namespace App\Services;

class ChatbotKnowledgeBase
{
    public function answerFor(string $question): ?string
    {
        $normalizedQuestion = $this->normalizeText($question);

        if ($normalizedQuestion === '') {
            return null;
        }

        if ($this->containsAny($normalizedQuestion, [
            'alur website',
            'flow website',
            'cara kerja website',
            'gimana alur website',
            'gimana flow website',
            'pakai website',
            'cara pakai website',
        ])) {
            return "Alur utama website Green Point:\n\n"
                ."1. Masuk atau daftar akun terlebih dahulu.\n"
                ."2. Setelah login, kamu masuk ke halaman Dashboard.\n"
                ."3. Dari sidebar, kamu bisa membuka Setor Sampah, Riwayat PPOB, Riwayat Setor, dan Profil Saya.\n"
                ."4. Jika ingin setor sampah, buka menu \"Setor Sampah\", isi form, lalu ajukan setor.\n"
                ."5. Setelah setor diproses dan disetujui admin, nilai setoran masuk ke saldo akunmu.\n"
                ."6. Saldo itu bisa dipakai untuk layanan PPOB di dashboard seperti E-money, Pulsa, dan PLN.";
        }

        if (
            $this->containsAny($normalizedQuestion, [
                'urutannya gimana',
                'alur setelah login',
                'setelah login',
                'baru login',
                'dari awal sampai akhir',
                'hasilnya dipakai buat apa',
                'saldo dipakai buat apa',
            ])
            && $this->containsAny($normalizedQuestion, [
                'setor',
                'sampah',
                'saldo',
                'hasil',
            ])
        ) {
            return "Kalau kamu sudah login dan ingin mengikuti alur utama website Green Point:\n\n"
                ."1. Buka menu \"Setor Sampah\".\n"
                ."2. Pilih jenis sampah, isi berat minimal 1 kg, lalu tekan \"+ Tambah Item\".\n"
                ."3. Upload foto untuk setiap item, cek total berat dan total nilai, lalu tekan \"Ajukan Setor\".\n"
                ."4. Tunggu admin memeriksa dan menyetujui setoranmu.\n"
                ."5. Setelah disetujui, nilai setoran masuk ke saldo akunmu.\n"
                ."6. Saldo itu bisa dipakai di dashboard untuk layanan E-money, Pulsa, atau PLN.\n\n"
                ."Kalau yang kamu maksud mencairkan saldo menjadi uang tunai, fitur itu belum tersedia di website saat ini.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'habis setor',
            'setelah setor',
            'abis setor',
            'dapet duit',
            'dapat duit',
            'dapetin duit',
            'dapet uang',
            'dapat uang',
            'dapetin uang',
            'uangnya masuk kemana',
            'uang dari sampah',
            'saldo dari sampah',
            'hasil setor',
            'hasil setoran',
        ])) {
            return "Setelah kamu mengajukan setor sampah, statusnya masih menunggu persetujuan admin.\n\n"
                ."1. Admin akan memeriksa detail setor dan foto yang kamu kirim.\n"
                ."2. Jika item disetujui, nilai setoran yang disetujui akan ditambahkan ke saldo akunmu.\n"
                ."3. Saldo itu bisa kamu lihat di halaman Profil Saya atau di halaman layanan PPOB.\n"
                ."4. Di website saat ini, saldo tersebut bisa dipakai untuk E-money, Pulsa, dan PLN.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'cairin jadi duit',
            'cairkan jadi duit',
            'cairin uang',
            'cairkan uang',
            'tarik tunai',
            'tarik saldo',
            'withdraw',
            'jadi uang cash',
            'jadi uang tunai',
            'transfer bank',
        ])) {
            return "Di website Green Point yang sekarang, menu nasabah untuk tarik tunai atau transfer saldo ke rekening belum tersedia.\n\n"
                ."Yang tersedia saat ini adalah:\n"
                ."1. Setor sampah sampai disetujui admin agar nilainya masuk ke saldo.\n"
                ."2. Menggunakan saldo itu untuk layanan PPOB seperti E-money, Pulsa, dan PLN.\n\n"
                ."Kalau kamu butuh pencairan uang tunai, alurnya belum tersedia di website ini, jadi perlu menghubungi admin Green Point.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'setor sampah',
            'ajukan setor',
            'jenis sampah',
            'hitung total',
            'cara setor',
        ])) {
            return "Untuk setor sampah:\n\n"
                ."1. Dari sidebar website, buka menu \"Setor Sampah\".\n"
                ."2. Pilih jenis sampah.\n"
                ."3. Isi berat minimal 1 kg.\n"
                ."4. Tekan \"+ Tambah Item\" supaya item masuk ke daftar.\n"
                ."5. Upload foto untuk setiap item yang kamu tambahkan.\n"
                ."6. Cek total berat dan total nilai yang muncul di form.\n"
                ."7. Kalau sudah benar, tekan tombol \"Ajukan Setor\".\n"
                ."8. Setelah itu, tunggu persetujuan admin.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'daftar',
            'registrasi',
            'buat akun',
            'bikin akun',
            'akun baru',
            'sign up',
        ])) {
            return "Untuk daftar akun Green Point:\n\n"
                ."1. Dari halaman awal, pilih tombol \"Daftar\".\n"
                ."2. Isi Nama Lengkap, Username, Email, Alamat, Nomor Telepon, Password minimal 8 karakter yang mengandung huruf besar, huruf kecil, angka, dan karakter khusus (!@#$%^&*), lalu Confirm Password.\n"
                ."3. Tekan tombol \"Daftar\".\n"
                ."4. Untuk daftar manual, klik link verifikasi yang dikirim ke emailmu.\n"
                ."5. Setelah email berhasil diverifikasi, masuk dengan email atau username dan password akunmu.\n"
                ."6. Kalau mau daftar dengan Google, pilih \"Daftar dengan Google\" supaya verifikasi email manual tidak diperlukan.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'cara masuk',
            'mau masuk',
            'masuk akun',
            'masuk website',
            'masuknya',
            'cara login',
            'login akun',
            'log in',
            'sign in',
        ])) {
            return "Untuk masuk ke akun Green Point:\n\n"
                ."1. Dari halaman awal, pilih tombol \"Masuk\".\n"
                ."2. Isi Email atau Username dan Password.\n"
                ."3. Tekan tombol \"Masuk\".\n"
                ."4. Kamu juga bisa memakai tombol \"Masuk dengan Google\".\n"
                ."5. Jika berhasil, kamu akan masuk ke dashboard.";
        }

        if ($this->containsAny($normalizedQuestion, ['e money', 'emoney', 'gopay', 'dana'])) {
            return "Untuk menggunakan E-Money:\n\n"
                ."1. Dari dashboard, pilih menu \"E-Money\".\n"
                ."2. Isi No Tujuan.\n"
                ."3. Pilih kategori nominal.\n"
                ."4. Pilih layanan seperti GoPay atau DANA.\n"
                ."5. Tekan \"Proses\".\n"
                ."6. Pastikan saldo mencukupi sebelum mengirim permintaan.";
        }

        if ($this->containsAny($normalizedQuestion, ['pln', 'token listrik', 'token pln'])) {
            return "Untuk membeli token PLN:\n\n"
                ."1. Dari dashboard, pilih menu \"PLN\".\n"
                ."2. Masukkan No Meter/Token.\n"
                ."3. Pilih nominal token.\n"
                ."4. Tekan \"Beli Token\".";
        }

        if ($this->containsAny($normalizedQuestion, ['pulsa', 'isi pulsa'])) {
            return "Untuk isi pulsa:\n\n"
                ."1. Dari dashboard, pilih menu \"Pulsa\".\n"
                ."2. Masukkan nomor telepon.\n"
                ."3. Pilih operator.\n"
                ."4. Pilih nominal pulsa.\n"
                ."5. Tekan \"Beli Pulsa\".";
        }

        if ($this->containsAny($normalizedQuestion, [
            'riwayat setor',
            'riwayat sampah',
            'history setor',
        ])) {
            return "Untuk melihat riwayat setor sampah:\n\n"
                ."1. Buka menu \"Riwayat\" di navigasi bawah.\n"
                ."2. Lihat daftar riwayat setor sampah.\n"
                ."3. Gunakan filter periode tanggal jika ingin mencari transaksi tertentu.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'transaksi ppob',
            'daftar transaksi',
            'status transaksi',
        ])) {
            return "Untuk melihat transaksi PPOB:\n\n"
                ."1. Buka menu \"Transaksi\" di navigasi bawah.\n"
                ."2. Lihat daftar transaksi yang tersedia.\n"
                ."3. Gunakan filter periode tanggal bila perlu.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'profil',
            'perbarui profil',
            'ubah profil',
            'edit profil',
        ])) {
            return "Untuk melihat atau mengubah profil:\n\n"
                ."1. Buka menu \"Profil\" di navigasi bawah.\n"
                ."2. Tekan \"Perbarui Profil\" untuk mengubah nama, username, email, alamat, atau nomor HP.\n"
                ."3. Dari halaman profil, kamu juga bisa keluar dari akun lewat ikon logout.";
        }

        if ($this->containsAny($normalizedQuestion, [
            'cara pakai',
            'cara menggunakan',
            'gunakan aplikasi',
            'tutorial',
        ])) {
            return "Alur utama website Green Point:\n\n"
                ."1. Daftar atau masuk terlebih dahulu.\n"
                ."2. Setelah login, kamu akan masuk ke Dashboard.\n"
                ."3. Gunakan menu \"Setor Sampah\" untuk mengajukan setoran baru.\n"
                ."4. Gunakan layanan PPOB di dashboard untuk E-money, Pulsa, atau PLN.\n"
                ."5. Buka \"Riwayat PPOB\" untuk melihat transaksi layanan.\n"
                ."6. Buka \"Riwayat Setor\" untuk melihat pengajuan setor sampah.\n"
                ."7. Buka \"Profil Saya\" untuk melihat data akun dan saldo.";
        }

        if ($this->containsAny($normalizedQuestion, ['fitur', 'menu', 'layanan apa'])) {
            return 'Fitur utama website Green Point saat ini meliputi Dashboard, Setor Sampah, E-money, Pulsa, PLN, '
                .'Riwayat PPOB, Riwayat Setor, dan Profil Saya.';
        }

        if ($this->containsAny($normalizedQuestion, [
            'limit',
            'kuota',
            'quota',
            'habis',
            'ai sibuk',
        ])) {
            return 'Kalau AI sedang limit, artinya batas penggunaan sementara sudah tercapai. '
                .'Tunggu beberapa saat sampai indikator di kanan atas normal kembali.';
        }

        if ($this->containsAny($normalizedQuestion, [
            'terima kasih',
            'makasih',
            'thanks',
            'thank you',
        ])) {
            return 'Sama-sama! Senang bisa membantu kamu.';
        }

        if ($this->containsAny($normalizedQuestion, [
            'error',
            'gagal',
            'tidak bisa',
            'bug',
            'masalah',
        ])) {
            return 'Maaf kalau ada kendala. Coba periksa kembali data yang diisi, tutup lalu buka ulang aplikasi, '
                .'dan ulangi prosesnya. Jika masih bermasalah, hubungi admin atau pengembang Green Point.';
        }

        if ($this->containsAny($normalizedQuestion, [
            'green point',
            'greenpoint',
            'aplikasi ini',
            'website ini',
            'tentang aplikasi',
        ])) {
            return 'Green Point adalah website bank sampah yang membantu nasabah mengajukan setor sampah, '
                .'melihat riwayat setor, memantau saldo, dan memakai saldo untuk layanan PPOB seperti E-money, Pulsa, dan PLN.';
        }

        if ($this->containsAny($normalizedQuestion, [
            'halo',
            'hallo',
            'hai',
            'hello',
            'hi',
            'pagi',
            'siang',
            'sore',
            'malam',
        ])) {
            return 'Halo! Saya Si Jajang. Ada yang bisa saya bantu hari ini?';
        }

        return null;
    }

    private function normalizeText(string $value): string
    {
        $normalized = mb_strtolower($value);
        $normalized = preg_replace('/[^a-z0-9\s]/u', ' ', $normalized) ?? '';
        $normalized = preg_replace('/\s+/u', ' ', $normalized) ?? '';

        return trim($normalized);
    }

    /**
     * @param  array<int, string>  $keywords
     */
    private function containsAny(string $question, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($question, $keyword)) {
                return true;
            }
        }

        return false;
    }
}
