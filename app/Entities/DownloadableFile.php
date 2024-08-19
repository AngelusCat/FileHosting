<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Services\FilesTDG;
use Illuminate\Support\Facades\Storage;

class DownloadableFile extends File
{
    private string $content;

    private FilesTDG $filesTDG;
    public function __construct(Disk $disk, string $nameToSave, string $originalName, string $content)
    {
        parent::__construct($disk, $nameToSave, $originalName);
        $this->content = $content;
    }

    public function upload(): int
    {
        Storage::disk($this->disk->name)->put($this->getFolders($this->nameToSave) . $this->nameToSave, $this->content);
        return $this->filesTDG->save($this->disk, $this->nameToSave, $this->originalName);
    }
}
