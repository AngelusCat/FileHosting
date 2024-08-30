<?php

namespace App\Entities;

use Illuminate\Support\Facades\Storage;

class PublicFile extends File
{
    public function getDownloadPath(): string
    {
        return storage_path('app/public/images') . "/$this->id/$this->nameToSave";
    }

    public function save(string $content): void
    {
        $this->securityStatus = $this->antivirus->getSecurityStatus($this->nameToSave, $content);
        $this->id = $this->filesTDG->save($this->disk, $this->nameToSave, $this->securityStatus, $this->originalName, $this->size, $this->uploadDate, $this->description);
        Storage::disk($this->disk->name)->put("/$this->id/" . $this->nameToSave, $content);
    }

    public function deleteAfterDownloading(): bool
    {
        return false;
    }
}
