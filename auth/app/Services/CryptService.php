<?php

namespace App\Services;

use App\Models\CarrierKey;
use App\Models\UserToken;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CryptService
{
    private const CIPHER = 'aes-256-cbc';

    private readonly string $key;

    public function __construct()
    {
        $this->key = config('app.master_key', '');
    }

    public function encode($data, $key = null): string
    {
        $key = $key ?: $this->key;

        $encrypter = new Encrypter(md5((string) $key), self::CIPHER);

        $data = base64_encode(
            // mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $data, MCRYPT_MODE_CBC, md5(md5($key)))
            $encrypter->encryptString($data)
        );

        return str_replace(['+', '/'], ['_', '-'], $data);

    }

    public function decode($encrypted, $key = null): string
    {
        $encrypted = str_replace(['_', '-'], ['+', '/'], (string) $encrypted);

        $encrypted = base64_decode($encrypted);

        $key = $key ?: $this->key;

        $encrypter = new Encrypter(md5((string) $key), self::CIPHER);

        return rtrim(
            // mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encrypted), MCRYPT_MODE_CBC, md5(md5($key))),
            $encrypter->decryptString($encrypted),
            "\0"
        );
    }

    public function getRandomKey(): string
    {
        $data = base64_encode(
            Encrypter::generateKey(self::CIPHER)
        );

        return str_replace(['+', '/'], ['_', '-'], $data);
    }

    public function createCredentialsFromCarrierKey(CarrierKey $carrierKey, int $id = null): array
    {
        Log::debug("going to create credentials from Carrier Key = $carrierKey->carrier_id, user ID = $id");
        if (!$id) {
            $userToken = Auth::getUser();
            if ($userToken instanceof UserToken) {
                $id = $userToken->user_id;
            }
        }
        $decodedCarrierKey = $this->decode($carrierKey->key);
        $uniqueKey = $this->getRandomKey();
        $personalKey = $this->encode($decodedCarrierKey, $uniqueKey);
        $data = [
            'user_id' => $id,
            'token' => $personalKey,
            'secret' => $uniqueKey,
        ];

        $saveData = $data;
        $saveData['secret'] = password_hash($saveData['secret'], PASSWORD_BCRYPT);

        UserToken::query()->create($saveData);

        Log::debug('new user token was generated and saved');
        Log::debug(implode('', $saveData));

        return $data;
    }
}
