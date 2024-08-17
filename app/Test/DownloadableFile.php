<?php

namespace app\Test;

use App\Enums\Disk;
use Illuminate\Support\Facades\Storage;

class DownloadableFile extends File
{
    private string $content;

    public function __construct(Disk $disk, string $nameToSave, string $originalName, string $content)
    {
        parent::__construct($disk, $nameToSave, $originalName);
        $this->content = $content;
    }

    public function upload(): void
    {
        Storage::disk($this->disk->name)->put($this->getFolders($this->nameToSave) . $this->nameToSave, $this->content);
        //Обратиться к БД для вставки записи nameToSave, disk, originalName
    }
}
