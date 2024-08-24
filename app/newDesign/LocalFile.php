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
        $this->filesTDG->save($this->disk, $this->nameToSave, $this->originalName);
    }
}
