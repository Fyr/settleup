<?php

class Application_Model_Base_CryptAdvanced extends Application_Model_Base_Crypt
{
    protected $cipher = 'aes-256-cbc'; // Using AES with a 256-bit block size in CBC mode

    public function __construct()
    {
        $key = 'FA54ABC3642345353453453AF5CBF674';
        $this->salt = pack('H*', $key);
    }

    public function encrypt($data, $key = null)
    {
        $key = $key ?: $this->salt;
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
        $data = base64_encode($iv . openssl_encrypt($data, $this->cipher, md5((string) $key), OPENSSL_RAW_DATA, $iv));
        $data = str_replace('+', '_', $data);
        $data = str_replace('/', '-', $data);

        return $data;
    }

    public function decrypt($encrypted, $key = null)
    {
        $encrypted = str_replace('_', '+', (string) $encrypted);
        $encrypted = str_replace('-', '/', $encrypted);
        $key = $key ?: $this->salt;
        $ivSize = openssl_cipher_iv_length($this->cipher);
        $iv = substr(base64_decode($encrypted), 0, $ivSize);
        if ($ivSize !== strlen($iv)) {
            return $encrypted;
        }
        $encryptedData = substr(base64_decode($encrypted), $ivSize);

        return rtrim(openssl_decrypt($encryptedData, $this->cipher, md5((string) $key), OPENSSL_RAW_DATA, $iv), "\0");
    }
}
