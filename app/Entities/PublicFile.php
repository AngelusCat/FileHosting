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
        $fileId = $this->filesTDG->save($this->disk, $this->nameToSave);
        Storage::disk($this->disk->name)->put("/$fileId/" . $this->nameToSave, $content);
    }

    public function deleteAfterDownloading(): bool
    {
        return false;
    }
}
