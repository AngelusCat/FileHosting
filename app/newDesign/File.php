<?php

namespace App\newDesign;

use App\Enums\Disk;
use App\Services\FilesTDG;
use Illuminate\Support\Facades\Storage;

abstract class File
{
    protected Disk $disk;
    protected string $nameToSave;

    protected FilesTDG $filesTDG;

    abstract public function getDownloadPath(int $id): string;
    abstract public function save(string $content): void;
}
