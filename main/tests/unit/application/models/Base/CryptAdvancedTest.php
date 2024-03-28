<?php
use PHPUnit\Framework\TestCase;

final class CryptAdvancedTest extends TestCase
{
    public function testEncryptDecrypt()
    {
        $cryptAdvanced = new Application_Model_Base_CryptAdvanced();

        $originalData = "testDataAdvanced";
        $encryptedData = $cryptAdvanced->encrypt($originalData);
        $decryptedData = $cryptAdvanced->decrypt($encryptedData);

        // Ensure that encrypted data is different from the original
        $this->assertNotEquals($originalData, $encryptedData);

        // Ensure that decrypted data matches the original
        $this->assertEquals($originalData, $decryptedData);
    }

    public function testCustomKeyEncryptDecrypt()
    {
        $cryptAdvanced = new Application_Model_Base_CryptAdvanced();

        $customKey = "customKey123";
        $originalData = "testDataWithCustomKey";
        $encryptedData = $cryptAdvanced->encrypt($originalData, $customKey);
        $decryptedData = $cryptAdvanced->decrypt($encryptedData, $customKey);

        // Ensure that encrypted data is different from the original
        $this->assertNotEquals($originalData, $encryptedData);

        // Ensure that decrypted data matches the original
        $this->assertEquals($originalData, $decryptedData);
    }
}
