<?php

namespace App\Helpers;

/**
 * ApplicationIdEncoder
 *
 * Encodes integer application IDs into short opaque strings for use in
 * candidate-facing edit links, e.g. /apply/edit/xK9mP2
 *
 * This is NOT cryptographic security — it just avoids exposing raw sequential
 * IDs in URLs. For stronger security, swap the key for a long random string
 * stored in your .env as APP_ID_ENCODER_KEY.
 *
 * Usage:
 *   $encoded = ApplicationIdEncoder::encode(45);   // → "xK9mP2"
 *   $id      = ApplicationIdEncoder::decode("xK9mP2"); // → 45
 */
class ApplicationIdEncoder
{
    private static string $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private static int    $base     = 62;

    public static function encode(int $id): string
    {
        // XOR with key to obscure the value before encoding
        $key    = static::numericKey();
        $mixed  = $id ^ $key;

        $result = '';
        while ($mixed > 0) {
            $result  = self::$alphabet[$mixed % self::$base] . $result;
            $mixed   = intdiv($mixed, self::$base);
        }

        // Pad to at least 6 characters so every link looks similar length
        return str_pad($result ?: '0', 6, self::$alphabet[0], STR_PAD_LEFT);
    }

    public static function decode(string $encoded): int
    {
        $value = 0;
        $len   = strlen($encoded);
        for ($i = 0; $i < $len; $i++) {
            $pos    = strpos(self::$alphabet, $encoded[$i]);
            if ($pos === false) return 0; // invalid character
            $value  = $value * self::$base + $pos;
        }

        $key = static::numericKey();
        return $value ^ $key;
    }

    /** Derives a stable integer key from APP_ID_ENCODER_KEY in .env */
    private static function numericKey(): int
    {
        $raw = config('app.id_encoder_key', env('APP_ID_ENCODER_KEY', 'default-key-change-me'));
        return abs(crc32($raw)) % PHP_INT_MAX;
    }
}