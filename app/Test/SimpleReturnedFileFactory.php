<?php

namespace app\Test;

use App\Enums\Disk;

readonly class SimpleReturnedFileFactory
{
    public function __construct(private FilesTDG $filesTDG){}
    public function create(int $id): ReturnedFile
    {
        $data = $this->filesTDG->findById($id);
        $disk = ($data->disk === 'public') ? Disk::public : Disk::local;
        return new ReturnedFile($disk, $data->name_to_save, $data->original_name);
    }
}
