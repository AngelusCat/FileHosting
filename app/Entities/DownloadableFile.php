<?php

namespace App\Entities;

use App\Enums\Disk;
use App\Services\FilesTDG;
use Illuminate\Support\Facades\Storage;

class DownloadableFile extends File
{
    private string $content;

    private FilesTDG $filesTDG;
    public function __construct(Disk $disk, string $nameToSave, string $originalName, string $content, FilesTDG $filesTDG)
    {
        parent::__construct($disk, $nameToSave, $originalName);
        $this->content = $content;
        $this->filesTDG = $filesTDG;
    }

    public function upload(): int
    {

        /**
         * TODO: ... поставить запись в БД выше сохранения файла в файловую систему, добавить id в путь app/public/images/ex/am/id/example.png
         * Сделать вправки в метод ReturnedFile getPathToDownloadFile()
         */

        Storage::disk($this->disk->name)->put($this->getFolders($this->nameToSave) . $this->nameToSave, $this->content);
        return $this->filesTDG->save($this->disk, $this->nameToSave, $this->originalName);
    }
}
