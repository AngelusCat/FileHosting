<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use App\Interfaces\Antivirus;
use App\Services\FilesTDG;
use App\Services\VirusTotal;

abstract class File
{
    protected Disk $disk;
    protected string $nameToSave;
    protected FilesTDG $filesTDG;
    protected Antivirus  $antivirus;
    protected SecurityStatus $securityStatus;

    public function __construct(Disk $disk, string $nameToSave, SecurityStatus $securityStatus = SecurityStatus::unknown) {
        $this->disk = $disk;
        $this->nameToSave = $nameToSave;
        $this->filesTDG = new FilesTDG();
        $this->antivirus = new VirusTotal();
        $this->securityStatus = $securityStatus;
    }

    abstract public function getDownloadPath(int $id): string;
    abstract public function save(string $content): void;

    abstract public function deleteAfterDownloading(): bool;
}
