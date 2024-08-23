<?php

namespace App\Entities;

use App\Enums\Disk;
use Illuminate\Support\Facades\Storage;

class ReturnedFile extends File
{
    public function getPathToDownloadFile(): string
    {
        $currentPath = $this->getStartOfPath($this->disk) . '/' . $this->getFolders($this->nameToSave) . $this->nameToSave;

        if ($this->disk->name === 'public') {
            return $currentPath;
        }

        //$currentPath = $this->getStartOfPath($this->disk) . '/' . $this->getFolders($this->nameToSave) . $this->nameToSave;

        Storage::makeDirectory('temporaryStorage/' . $this->getFolders($this->originalName));

        $copyPath = $this->getStartOfPath($this->disk) . '/temporaryStorage/' . $this->getFolders($this->originalName) . $this->nameToSave;
        copy($currentPath, $copyPath);

        $renamePath = $this->getStartOfPath($this->disk) . '/temporaryStorage/' . $this->getFolders($this->originalName) . $this->originalName;
        rename($copyPath, $renamePath);

        //return '/temporaryStorage/' . $this->getFolders($this->originalName) . $this->originalName;
        return $renamePath;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function getDisk(): Disk
    {
        return $this->disk;
    }

    private function getStartOfPath(Disk $disk): string
    {
        return ($disk->name === 'public') ? storage_path('app/public/images') : storage_path('app/files');
    }
}
