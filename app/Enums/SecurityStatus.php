<?php

namespace App\Enums;

enum SecurityStatus: string
{
    case safe = "безопасный";
    case doubtful = "подозрительный";
    case malicious = "вредоносный";
    case unknown = "не проверен";

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
