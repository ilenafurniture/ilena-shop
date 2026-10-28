<?php

namespace Tests\Unit;

use App\Services\SmartProductAssistant;
use CodeIgniter\Test\CIUnitTestCase;

final class ChatAssistantEvaluationTest extends CIUnitTestCase
{
    /** @dataProvider deterministicCases */
    public function testPolicyAndScopeCasesAreDeterministicWithoutDatabaseOrProvider(array $case): void
    {
        $result = (new SmartProductAssistant(new \stdClass()))->respond($case['query']);

        $this->assertStringContainsString($case['must_contain'], $result['answer']);
        $this->assertSame([], $result['products']);
        $this->assertSame([], $result['sources']);
        $this->assertSame('policy', $result['mode']);
    }

    public function testNoEvidenceUsesFallbackWithoutAttemptingProvider(): void
    {
        $db = \Config\Database::connect('tests');
        $forge = \Config\Database::forge('tests');
        if ($db->tableExists('barang')) $forge->dropTable('barang', true);
        $forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => 64],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 191],
            'harga' => ['type' => 'INT'],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'kategori' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'subkategori' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'varian' => ['type' => 'TEXT', 'null' => true],
            'diskon' => ['type' => 'INT', 'null' => true],
            'active' => ['type' => 'VARCHAR', 'constraint' => 1],
            'pengunjung' => ['type' => 'INT', 'default' => 0],
        ]);
        $forge->addKey('id', true);
        $forge->createTable('barang', true);
        try {
            $result = (new SmartProductAssistant($db))->respond('Apa rekomendasi kursi untuk ruang keluarga?');
            $this->assertSame('fallback', $result['mode']);
            $this->assertSame('no_retrieval_evidence', $result['fallbackReason']);
            $this->assertStringContainsString('belum menemukan informasi', mb_strtolower($result['answer']));
        } finally {
            $forge->dropTable('barang', true);
        }
    }

    public static function deterministicCases(): array
    {
        return array_map(static fn(array $case): array => [$case], require dirname(__DIR__) . '/fixtures/kak_wawa_eval_cases.php');
    }
}
