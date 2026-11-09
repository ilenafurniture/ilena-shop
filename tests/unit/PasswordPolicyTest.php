<?php

use App\Services\Auth\PasswordPolicy;
use CodeIgniter\Test\CIUnitTestCase;

final class PasswordPolicyTest extends CIUnitTestCase
{
    public function testAcceptsEightCharacterPasswordWithThreeCharacterTypes(): void
    {
        $this->assertTrue(PasswordPolicy::isStrong('Wood123!'));
    }

    public function testAcceptsLongMemorablePassphraseWithTwoCharacterTypes(): void
    {
        $this->assertTrue(PasswordPolicy::isStrong('wooden room warm'));
    }

    public function testRejectsShortOrLowVarietyPasswords(): void
    {
        $this->assertFalse(PasswordPolicy::isStrong('Wood123'));
        $this->assertFalse(PasswordPolicy::isStrong('ilena123'));
        $this->assertFalse(PasswordPolicy::isStrong('Password1!'));
    }

    public function testRejectsPasswordsOverBcryptByteLimitAndNullBytes(): void
    {
        $this->assertFalse(PasswordPolicy::isStrong(str_repeat('A', 73)));
        $this->assertFalse(PasswordPolicy::isStrong("Strong123!\0"));
    }
}
