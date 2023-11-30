<?php

declare(strict_types=1);

namespace App\Helper;

class MathHelper
{
    private static function getDecimalPlaces(float $number): int
    {
        if (false === $decimalPointPosition = strpos((string)$number, '.')) {
            return 0;
        }

        return strlen(substr((string)$number, ++$decimalPointPosition));
    }

    public static function sumFloats(float $a, float $b): float
    {
        $factor = pow(10, max(self::getDecimalPlaces($a), self::getDecimalPlaces($b)));

        return (float)((int)($a * $factor) + (int)($b * $factor)) / $factor;
    }
}