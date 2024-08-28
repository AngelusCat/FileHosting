<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use Illuminate\Support\Facades\Storage;

class LocalFile extends File
{
    private string $originalName;

    public function __construct(Disk $disk, string $nameToSave, string $originalName, SecurityStatus $securityStatus = SecurityStatus::unknown)
    {
        parent::__construct($disk, $nameToSave, $securityStatus);
        $this->originalName = $originalName;
    }
    private function getFolders(string $fileName): string
    {
        return mb_substr($fileName, 0, 2) . '/' . mb_substr($fileName, 2, 2) . '/';
    }

    public function getDownloadPath(int $id): string
    {
        return $this->prepareForDownload($id);
    }

    private function prepareForDownload(int $id): string
    {
        $savePath = storage_path("app/files/") . $this->getFolders($this->nameToSave) . $this->nameToSave;
        $copyPath = storage_path("app/files/") . "tmp/$id/$this->nameToSave";
        Storage::disk($this->disk->name)->makeDirectory("tmp/$id/");
        copy($savePath, $copyPath);
        $renamePath = storage_path("app/files/") . "tmp/$id/$this->originalName";
        rename($copyPath, $renamePath);
        return $renamePath;
    }

    public function save(string $content): void
    {
        Storage::disk($this->disk->name)->put($this->getFolders($this->nameToSave) . $this->nameToSave, $content);
        $this->securityStatus = $this->antivirus->getSecurityStatus($this->nameToSave, $content);
        $this->filesTDG->save($this->disk, $this->nameToSave, $this->securityStatus, $this->originalName);
    }

    public function deleteAfterDownloading(): bool
    {
        return true;
    }
}
