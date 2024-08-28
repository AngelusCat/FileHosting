<?php

namespace App\Entities;

use Illuminate\Support\Facades\Storage;

class PublicFile extends File
{
    public function getDownloadPath(int $id): string
    {
        return storage_path('app/public/images') . "/$id/$this->nameToSave";
    }

    public function save(string $content): void
    {
        $this->securityStatus = $this->antivirus->getSecurityStatus($this->nameToSave, $content);
        $fileId = $this->filesTDG->save($this->disk, $this->nameToSave, $this->securityStatus);
        Storage::disk($this->disk->name)->put("/$fileId/" . $this->nameToSave, $content);
    }

    public function deleteAfterDownloading(): bool
    {
        return false;
    }
}
