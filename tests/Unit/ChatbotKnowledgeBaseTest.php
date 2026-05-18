<?php

namespace Tests\Unit;

use App\Services\ChatbotKnowledgeBase;
use PHPUnit\Framework\TestCase;

class ChatbotKnowledgeBaseTest extends TestCase
{
    public function test_it_explains_the_web_flow(): void
    {
        $answer = (new ChatbotKnowledgeBase())->answerFor('gimana alur websitenya');

        $this->assertNotNull($answer);
        $this->assertStringContainsString('Dashboard', $answer);
        $this->assertStringContainsString('Riwayat PPOB', $answer);
        $this->assertStringContainsString('Riwayat Setor', $answer);
    }

    public function test_it_explains_how_to_submit_waste_on_the_website(): void
    {
        $answer = (new ChatbotKnowledgeBase())->answerFor('gimana cara setor sampah');

        $this->assertNotNull($answer);
        $this->assertStringContainsString('"+ Tambah Item"', $answer);
        $this->assertStringContainsString('"Ajukan Setor"', $answer);
        $this->assertStringContainsString('persetujuan admin', $answer);
    }

    public function test_it_explains_what_happens_after_waste_submission(): void
    {
        $answer = (new ChatbotKnowledgeBase())->answerFor('habis setor sampah aku dapet duit gimana');

        $this->assertNotNull($answer);
        $this->assertStringContainsString('nilai setoran', $answer);
        $this->assertStringContainsString('saldo akunmu', $answer);
        $this->assertStringContainsString('E-money, Pulsa, dan PLN', $answer);
    }

    public function test_it_does_not_invent_cash_withdrawal_flow(): void
    {
        $answer = (new ChatbotKnowledgeBase())->answerFor('cara cairin jadi duitnya gimana');

        $this->assertNotNull($answer);
        $this->assertStringContainsString('belum tersedia', $answer);
        $this->assertStringContainsString('menghubungi admin Green Point', $answer);
    }

    public function test_it_prefers_end_to_end_flow_over_generic_login_keywords(): void
    {
        $answer = (new ChatbotKnowledgeBase())->answerFor(
            'aku baru login, kalau mau setor terus hasilnya dipakai buat apa urutannya gimana?'
        );

        $this->assertNotNull($answer);
        $this->assertStringContainsString('"Setor Sampah"', $answer);
        $this->assertStringContainsString('nilai setoran masuk ke saldo akunmu', $answer);
        $this->assertStringContainsString('E-money, Pulsa, atau PLN', $answer);
    }
}
