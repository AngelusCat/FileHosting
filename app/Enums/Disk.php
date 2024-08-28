<?php

namespace App\Enums;

enum Disk
{
    case public;
    case local;

    public static function getDiskByStringDisk(string $stringDisk): self
    {
        return match ($stringDisk) {
            'public' => self::public,
            'local' => self::local
        };
    }
}
