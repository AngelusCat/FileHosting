<?php

namespace app\Test;

use App\Enums\Disk;
use app\Test\File;

class ReturnedFile extends File
{
    public function unload(): string
    {
        $currentPath = $this->getStartOfPath($this->disk) . '/' . $this->getFolders($this->nameToSave) . '/' . $this->nameToSave;
        if ($this->disk->name === 'public') {
            return $currentPath;
        }
        $copyPath = $this->getStartOfPath($this->disk) . '/temporaryStorage/' . $this->getFolders($this->originalName) . '/' . $this->nameToSave;
        copy($currentPath, $copyPath);
        $renamePath = $this->getStartOfPath($this->disk) . '/temporaryStorage/' . $this->getFolders($this->originalName) . '/' . $this->originalName;
        rename($copyPath, $renamePath);
        return $renamePath;
    }

    private function getStartOfPath(Disk $disk): string
    {
        return ($disk->name === 'public') ? storage_path('app/public/images') : storage_path('app/files');
    }
}
