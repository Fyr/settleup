<?php

class Application_Model_Base_Crypt
{
    protected $cipher = 'aes-256-cbc'; // Using AES with a 256-bit block size in CBC mode
    protected $salt;
    protected $ivSize;

    public function __construct()
    {
        $key = 'FA54ABC3642345353453453AF5CBF674';
        $this->salt = pack('H*', $key);
        $this->ivSize = openssl_cipher_iv_length($this->cipher);
    }

    public function decrypt($data)
    {
        $encrypted = str_replace('_', '+', (string) $data);
        $encrypted = str_replace('-', '/', $encrypted);
        $iv = substr(base64_decode($encrypted), 0, $this->ivSize);
        $encryptedData = substr(base64_decode($encrypted), $this->ivSize);

        return rtrim(openssl_decrypt($encryptedData, $this->cipher, md5((string) $this->salt), OPENSSL_RAW_DATA, $iv), "\0");
    }

    public function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes($this->ivSize);
        $data = base64_encode($iv . openssl_encrypt($data, $this->cipher, md5((string) $this->salt), OPENSSL_RAW_DATA, $iv));
        $data = str_replace('+', '_', $data);
        $data = str_replace('/', '-', $data);

        return $data;
    }
}
