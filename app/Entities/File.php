<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Services\FilesTDG;

abstract class File
{
    protected Disk $disk;
    protected string $nameToSave;
    protected FilesTDG $filesTDG;

    public function __construct(Disk $disk, string $nameToSave) {
        $this->disk = $disk;
        $this->nameToSave = $nameToSave;
        $this->filesTDG = new FilesTDG();
    }

    abstract public function getDownloadPath(int $id): string;
    abstract public function save(string $content): void;

    abstract public function deleteAfterDownloading(): bool;
}
