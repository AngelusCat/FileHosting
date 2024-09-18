<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use App\Enums\ViewingStatus;
use App\Interfaces\Antivirus;
use App\Services\FilesTDG;
use App\Services\VirusTotal;
use Carbon\Carbon;
use Illuminate\Support\Arr;

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
    protected ViewingStatus $viewingStatus;
    protected FilesTDG $filesTDG;
    protected Antivirus  $antivirus;

    public function __construct(Disk $disk, string $nameToSave, string $originalName, int $size, Carbon $uploadDate, string $description, ViewingStatus $viewingStatus, SecurityStatus $securityStatus = SecurityStatus::unknown, int $id = null)
    {
        $this->id = $id;
        $this->disk = $disk;
        $this->nameToSave = $nameToSave;
        $this->originalName = $originalName;
        $this->size = $size;
        $this->uploadDate = $uploadDate;
        $this->description = $description;
        $this->viewingStatus = $viewingStatus;
        $this->securityStatus = $securityStatus;
        $this->filesTDG = resolve(FilesTDG::class);
        $this->antivirus = resolve(VirusTotal::class);
    }

    public function getId(): ?int
    {
        if ($this->id === null) {
            dd('Здесь нужно исключение');
        } else {
            return $this->id;
        }
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getUploadDate(): Carbon
    {
        return $this->uploadDate;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSecurityStatus(): SecurityStatus
    {
        return $this->securityStatus;
    }

    public function getViewingStatus(): ViewingStatus
    {
        return $this->viewingStatus;
    }

    public function getDisk(): Disk
    {
        return $this->disk;
    }

    protected function getListOfPropertiesThatNeedToBeSavedInDatabase(): array
    {
        $disk = $this->disk->name;
        $nameToSave = $this->nameToSave;
        $originalName = $this->originalName;
        $size = $this->size;
        $uploadDate = $this->uploadDate;
        $description = $this->description;
        $viewingStatus = $this->viewingStatus->name;
        $securityStatus = $this->securityStatus->name;
        return compact('disk', 'nameToSave', 'originalName', 'size', 'uploadDate', 'description', 'viewingStatus', 'securityStatus');
    }

    protected function getListOfPropertiesThatNeedToBeUpdatedInDatabase(): array
    {
        $originalName = $this->originalName;
        $description = $this->description;
        return compact('originalName', 'description');
    }

    public function changeMetadata(array $metadata): void
    {
        foreach ($metadata as $key => $value) {
            if ($key === "originalName" || $key === "nameToSave" && $this->disk->name === "public") {
                $extension = [];
                preg_match_all("/\.[a-zA-Z]{1,}$/", $this->originalName, $extension);
                $extension = Arr::collapse($extension)[0];
                $this->$key = $value . $extension;
                continue;
            }
            $this->$key = $value;
        }
        $this->filesTDG->update($this->id, $this->getListOfPropertiesThatNeedToBeUpdatedInDatabase());
    }

    abstract public function getDownloadPath(): string;
    abstract public function save(string $content): void;
    abstract public function deleteAfterDownloading(): bool;
}
