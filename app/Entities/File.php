<?php

namespace App\Entities;

use App\Enums\Disk;

abstract class File
{
    protected Disk $disk;
    protected string $nameToSave;
    protected string $originalName;

    public function __construct(Disk $disk, string $nameToSave, string $originalName)
    {
        $this->disk = $disk;
        $this->nameToSave = $nameToSave;
        $this->originalName = $originalName;
    }

    protected function getFolders(string $fileName): string
    {
        return mb_substr($fileName, 0, 2) . '/' . mb_substr($fileName, 2, 2) . '/';
    }
}
