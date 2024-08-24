<?php

namespace App\newDesign;

use App\newDesign\File;
use Illuminate\Support\Facades\Storage;

class LocalFile extends File
{
    private string $originalName;

    private function getFolders(string $fileName): string
    {
        return mb_substr($fileName, 0, 2) . '/' . mb_substr($fileName, 2, 2) . '/';
    }

    public function getDownloadPath(int $id): string
    {
        return "/tmp/$id/$this->originalName";
    }

    public function save(string $content): void
    {
        Storage::disk($this->disk->name)->put($this->getFolders($this->nameToSave) . $this->nameToSave, $content);
        $this->filesTDG->save($this->disk, $this->nameToSave, $this->originalName);
    }
}
