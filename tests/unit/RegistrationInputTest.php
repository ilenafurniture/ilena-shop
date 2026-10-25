<?php

namespace Tests\Unit;

use App\Services\Auth\RegistrationInput;
use App\Services\Auth\DisposableEmailDomainList;
use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;

final class RegistrationInputTest extends CIUnitTestCase
{
    private function valid(array $changes = []): array
    {
        return RegistrationInput::normalize(array_replace(['nama'=>'Pembeli Ilena','email'=>'buyer@example.com','nohp'=>'081234567890','sandi'=>'TwelveChars!123','validasi-syarat'=>'1'], $changes));
    }

    public function testNormalizedIdentityAndPhoneRetainLeadingZero(): void
    {
        $data = $this->valid(['email'=>' BUYER@Example.COM ', 'nama'=>' Pembeli Ilena ']);
        $this->assertSame('buyer@example.com', $data['email']);
        $this->assertSame('Pembeli Ilena', $data['nama']);
        $this->assertSame('081234567890', $data['nohp']);
        $this->assertSame([], (new RegistrationInput())->errors($data, static fn()=>false));
    }

    public function testOnlyAsciiDigitsWithSupportedLengthAreAccepted(): void
    {
        foreach(['abc','0812abc7890','08 1234567890','+628123456789','0812-34567890','081234567','0812345678901234','０８１２３４５６７８９０',"081234567890\n0"] as $phone) {
            $this->assertArrayHasKey('nohp', (new RegistrationInput())->errors($this->valid(['nohp'=>$phone]), static fn()=>false), $phone);
        }
        foreach(['081234567890','6281234567890','1234567890','123456789012345'] as $phone) {
            $this->assertArrayNotHasKey('nohp', (new RegistrationInput())->errors($this->valid(['nohp'=>$phone]), static fn()=>false));
        }
    }

    public function testInvalidEmailDisposableDomainAndArrayPayloadAreRejected(): void
    {
        $validator = new RegistrationInput();
        $this->assertArrayHasKey('email', $validator->errors($this->valid(['email'=>'not-an-email']), static fn()=>false));
        $this->assertArrayHasKey('email', $validator->errors($this->valid(['email'=>'buyer@mail.tm'])));
        $this->assertArrayHasKey('email', $validator->errors($this->valid(['email'=>'buyer@sub.mail.tm'])));
        $errors = $validator->errors($this->valid(['nama'=>['x'],'email'=>['x'],'nohp'=>['081234567890'],'sandi'=>['x'],'validasi-syarat'=>['1']]), static fn()=>false);
        foreach(['nama','email','nohp','sandi','validasi-syarat'] as $field) $this->assertArrayHasKey($field, $errors);
    }

    public function testPasswordByteLimitAgreementAndNames(): void
    {
        $validator = new RegistrationInput();
        foreach(['short',str_repeat('a',73),str_repeat('é',37),"valid-length-password\0"] as $password) {
            $this->assertArrayHasKey('sandi', $validator->errors($this->valid(['sandi'=>$password]), static fn()=>false));
        }
        $this->assertArrayHasKey('validasi-syarat', $validator->errors($this->valid(['validasi-syarat'=>'0']), static fn()=>false));
        foreach(['<script>',str_repeat('a',101),"A\nB"] as $name) $this->assertArrayHasKey('nama', $validator->errors($this->valid(['nama'=>$name]), static fn()=>false));
        $this->assertSame([], $validator->errors($this->valid(['nama'=>"D'Angelo",'sandi'=>str_repeat('a',72)]), static fn()=>false));
    }

    public function testBundledListWorksWithoutNetworkAndMatchesSubdomains(): void
    {
        $lines=file(APPPATH.'Data/disposable-email-domains.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
        $this->assertGreaterThan(1000, count($lines));
        $list=new DisposableEmailDomainList();
        $this->assertTrue($list->isListed($lines[0]));
        $this->assertTrue($list->isListed('sub.'.$lines[0]));
        $this->assertFalse($list->isListed('gmail.com'));
    }

    public function testDuplicateIdentityLookupNormalizesCaseAndLegacyWhitespace(): void
    {
        $db=\Config\Database::connect(['DBDriver'=>'SQLite3','database'=>':memory:','DBPrefix'=>'','DBDebug'=>true],false);
        try {
            $db->query('CREATE TABLE user (id INTEGER PRIMARY KEY, email TEXT)');
            $db->table('user')->insert(['id'=>1,'email'=>' Legacy@Example.COM ']);
            $model=new UserModel($db);
            $this->assertSame(1,(int)$model->findByEmailIdentity(' legacy@example.com ')['id']);
            $this->assertNull($model->findByEmailIdentity('other@example.com'));
        } finally { $db->close(); }
    }
}
