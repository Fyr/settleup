<?php
use PHPUnit\Framework\TestCase;

final class CryptTest extends TestCase
{
    public function testEncryptDecrypt()
    {
        $crypt = new Application_Model_Base_Crypt();

        $originalData = "testData";
        $encryptedData = $crypt->encrypt($originalData);
        $decryptedData = $crypt->decrypt($encryptedData);

        // Ensure that encrypted data is different from the original
        $this->assertNotEquals($originalData, $encryptedData);

        // Ensure that decrypted data matches the original
        $this->assertEquals($originalData, $decryptedData);
    }
}
