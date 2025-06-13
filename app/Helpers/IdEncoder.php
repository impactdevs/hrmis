<?php

namespace App\Helpers;

class IdEncoder
{
    protected static string $defaultCharset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    protected string $charset;
    protected int $base;
    protected bool $usePadding;
    protected int $padLength;
    protected string $negativePrefix;

    public function __construct(
        string $charset = null,
        bool $usePadding = false,
        int $padLength = 0,
        string $negativePrefix = '_'
    ) {
        $this->charset = $charset ?? self::$defaultCharset;
        $this->base = strlen($this->charset);
        $this->usePadding = $usePadding;
        $this->padLength = $padLength;
        $this->negativePrefix = $negativePrefix;

        if ($this->base < 2) {
            throw new \InvalidArgumentException("Charset must contain at least two characters.");
        }

        if (count(array_unique(str_split($this->charset))) !== $this->base) {
            throw new \InvalidArgumentException("Charset must not contain duplicate characters.");
        }
    }

    public function encode(int $int): string
    {
        $isNegative = $int < 0;
        $int = abs($int);

        if ($int === 0) {
            $encoded = $this->charset[0];
        } else {
            $encoded = '';
            while ($int > 0) {
                $encoded = $this->charset[$int % $this->base] . $encoded;
                $int = intdiv($int, $this->base);
            }
        }

        if ($this->usePadding && strlen($encoded) < $this->padLength) {
            $encoded = str_pad($encoded, $this->padLength, $this->charset[0], STR_PAD_LEFT);
        }

        return $isNegative ? $this->negativePrefix . $encoded : $encoded;
    }

    public function decode(string $str): int
    {
        $isNegative = false;

        if (str_starts_with($str, $this->negativePrefix)) {
            $isNegative = true;
            $str = substr($str, strlen($this->negativePrefix));
        }

        $int = 0;
        $length = strlen($str);

        for ($i = 0; $i < $length; $i++) {
            $pos = strpos($this->charset, $str[$i]);

            if ($pos === false) {
                throw new \InvalidArgumentException("Invalid character '{$str[$i]}' in input string.");
            }

            $int = $int * $this->base + $pos;
        }

        return $isNegative ? -$int : $int;
    }
}
