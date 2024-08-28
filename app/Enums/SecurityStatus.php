<?php

namespace App\Enums;

enum SecurityStatus
{
    case safe;
    case doubtful;
    case malicious;
    case unknown;

    public static function getSecurityStatusByStringStatus(string $stringStatus): self
    {
        return match ($stringStatus) {
            'safe' => self::safe,
            'doubtful' => self::doubtful,
            'malicious' => self::malicious,
            'unknown' => self::unknown
        };
    }
}
