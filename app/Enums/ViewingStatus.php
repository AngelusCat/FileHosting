<?php

namespace App\Enums;

enum ViewingStatus
{
    case public;
    case private;

    public static function getViewingStatusByStringStatus(string $stringStatus): self
    {
        return match ($stringStatus) {
            'public' => self::public,
            'private' => self::private
        };
    }
}
