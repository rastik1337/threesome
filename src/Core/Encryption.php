<?php

declare(strict_types=1);

namespace App\Core;

const ALGO = 'aes-256-cbc';

class Encryption
{
    public static function encrypt(mixed $rawData)
    {
        $encryptionKey = getenv('ENCRYPTION_KEY');
        $ivLength = openssl_cipher_iv_length(ALGO);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encryptedText = openssl_encrypt($rawData, ALGO, $encryptionKey, 0, $iv);

        return base64_encode($iv . $encryptedText);
    }

    public static function decrypt(string $data)
    {
        $decoded = base64_decode($data);

        $ivLength = openssl_cipher_iv_length(ALGO);
        $iv = substr($decoded, 0, $ivLength);
        $encryptedText = substr($decoded, $ivLength);

        $encryptionKey = getenv('ENCRYPTION_KEY');
        return openssl_decrypt($encryptedText, ALGO, $encryptionKey, 0, $iv);
    }
}
