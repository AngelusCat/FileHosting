<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use Illuminate\Support\Facades\Storage;

class LocalFile extends File
{
    private function getFolders(string $fileName): string
    {
        return mb_substr($fileName, 0, 2) . '/' . mb_substr($fileName, 2, 2) . '/';
    }

    public function getDownloadPath(): string
    {
        return $this->prepareForDownload();
    }

    private function prepareForDownload(): string
    {
        $savePath = storage_path("app/files/") . $this->getFolders($this->nameToSave) . $this->nameToSave;
        $copyPath = storage_path("app/files/") . "tmp/$this->id/$this->nameToSave";
        Storage::disk($this->disk->name)->makeDirectory("tmp/$this->id/");
        copy($savePath, $copyPath);
        $renamePath = storage_path("app/files/") . "tmp/$this->id/$this->originalName";
        rename($copyPath, $renamePath);
        return $renamePath;
    }

    public function save(string $content): void
    {
        Storage::disk($this->disk->name)->put($this->getFolders($this->nameToSave) . $this->nameToSave, $content);
        $this->securityStatus = $this->antivirus->getSecurityStatus($this->nameToSave, $content);
        $this->id = $this->filesTDG->save($this->getListOfPropertiesThatNeedToBeSavedInDatabase());
    }

    public function deleteAfterDownloading(): bool
    {
        return true;
    }
}
