<?php

namespace Feierstoff\ToolboxBundle\Encryption;

class Encryption {

    const AES_256_CTR = "aes-256-ctr";

    private string $key;
    private string $salt;
    private string $cipher;

    public static function createIV(string $cipher = Encryption::AES_256_CTR): string {
        return openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    }

    public static function createSalt(int $length = 16): string {
        return openssl_random_pseudo_bytes($length);
    }

    public static function createKey(string $data, $salt = null): string {
        if (!$salt) {
            $salt = self::createSalt();
        }

        return hash_pbkdf2("sha256", $data, $salt, 10, 0, true);
    }

    public static function encrypt(string $data, string $key, $cipher = self::AES_256_CTR) {
        $iv = self::createIV($cipher);
        $salt = self::createSalt();
        $key = hash_pbkdf2("sha256", $key, $salt, 10, 0, true);
        return $salt . "###" . $iv . "###" . openssl_encrypt($data, $cipher, $key, $options=0, $iv);
    }

    public static function decrypt(string $data, string $key, $cipher = self::AES_256_CTR): string | false {
        $parts = explode("###", $data);
        if (sizeof($parts) != 3) return false;
        $key = hash_pbkdf2("sha256", $key, $parts[0], 10, 0, true);
        return openssl_decrypt($parts[2], $cipher, $key, $options=0, $parts[1]);
    }
}