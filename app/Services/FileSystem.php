<?php

namespace App\Services;

use App\Entities\File;
use Illuminate\Support\Facades\Storage;

class FileSystem
{
    public function save(File $file): void
    {
        $path = ($file->isSecretPathUsed()) ? '/' . $this->getSecretPath($file->getFakeName()) : '/';
        $fileName = $file->getSaveName();
        Storage::disk($file->getDisk()->value)->put($path . $fileName, $file->getContent());
    }

    private function getSecretPath(string $fakeName): string
    {
        return mb_substr($fakeName, 0, 2) . '/' . mb_substr($fakeName, 2, 2) . '/';
    }
}
