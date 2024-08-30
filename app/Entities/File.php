<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use App\Interfaces\Antivirus;
use App\Services\FilesTDG;
use App\Services\VirusTotal;
use Carbon\Carbon;

abstract class File
{
    protected ?int $id;
    protected Disk $disk;
    protected string $nameToSave;
    protected SecurityStatus $securityStatus;
    protected string $originalName;
    protected int $size;
    protected Carbon $uploadDate;
    protected string $description;
    protected FilesTDG $filesTDG;
    protected Antivirus  $antivirus;

    public function __construct(Disk $disk, string $nameToSave, string $originalName, int $size, Carbon $uploadDate, string $description, SecurityStatus $securityStatus = SecurityStatus::unknown, int $id = null)
    {
        $this->id = $id;
        $this->disk = $disk;
        $this->nameToSave = $nameToSave;
        $this->originalName = $originalName;
        $this->size = $size;
        $this->uploadDate = $uploadDate;
        $this->description = $description;
        $this->securityStatus = $securityStatus;
        $this->filesTDG = new FilesTDG();
        $this->antivirus = new VirusTotal();
    }

    public function getId(): ?int
    {
        if ($this->id === null) {
            dd('Здесь нужно исключение');
        } else {
            return $this->id;
        }
    }

    abstract public function getDownloadPath(): string;
    abstract public function save(string $content): void;

    abstract public function deleteAfterDownloading(): bool;
}
