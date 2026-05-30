<?php

namespace App\Support;

class Base62
{
    private const ALPHABET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    public static function encode(int $number): string
    {
        if ($number === 0) {
            return '0';
        }

        $result = '';
        while ($number > 0) {
            $result = self::ALPHABET[$number % 62] . $result;
            $number = intdiv($number, 62);
        }

        return $result;
    }

    public static function decode(string $string): int
    {
        $result = 0;
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $result = $result * 62 + strpos(self::ALPHABET, $string[$i]);
        }
        return $result;
    }

    public static function fromId(int $id, int $minLength = 6): string
    {
        $offset = 1_000_000;
        $encoded = self::encode($id + $offset);
        return str_pad($encoded, $minLength, '0', STR_PAD_LEFT);
    }
}
