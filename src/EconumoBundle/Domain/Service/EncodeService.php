<?php

declare(strict_types=1);


namespace App\EconumoBundle\Domain\Service;


readonly class EncodeService implements EncodeServiceInterface
{
    public function __construct(private string $econumoSalt)
    {
    }

    public function hash(string $value): string
    {
        return md5($value . $this->econumoSalt);
    }

    public function encode(string $value): string
    {
        if (empty($this->econumoSalt)) {
            return $value;
        }

        $ivLen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = openssl_random_pseudo_bytes($ivLen);
        $ciphertextRaw = openssl_encrypt($value, $cipher, $this->econumoSalt, $options = OPENSSL_RAW_DATA, $iv);
        $hmac = hash_hmac('sha256', $ciphertextRaw, $this->econumoSalt, true);
        return base64_encode($iv . $hmac . $ciphertextRaw);
    }

    public function decode(string $value): ?string
    {
        if (empty($this->econumoSalt)) {
            return $value;
        }

        $c = base64_decode($value);
        $ivLen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
        $iv = substr($c, 0, $ivLen);
        $hmac = substr($c, $ivLen, 32);
        $ciphertextRaw = substr($c, $ivLen + 32);
        $originalPlainText = openssl_decrypt($ciphertextRaw, $cipher, $this->econumoSalt, OPENSSL_RAW_DATA, $iv);
        return hash_equals(
            $hmac,
            hash_hmac('sha256', $ciphertextRaw, $this->econumoSalt, true)
        ) ? $originalPlainText : null;
    }
}
