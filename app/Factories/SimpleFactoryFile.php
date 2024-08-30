<?php

namespace App\Factories;

use App\Enums\Disk;
use App\Entities\File;
use App\Entities\LocalFile;
use App\Entities\PublicFile;
use App\Enums\SecurityStatus;
use App\Services\FilesTDG;
use Illuminate\Http\UploadedFile;


class SimpleFactoryFile
{
    public function __construct(private FilesTDG $filesTDG){}
    public function createByUploadFile(UploadedFile $fileFromForm): File
    {
        $mimeType = [];
        preg_match('/image/', $fileFromForm->getMimeType(), $mimeType);
        $mimeType = $mimeType[0] ?? '';

        $disk = ($mimeType === 'image') ? Disk::public : Disk::local;
        $originalName = preg_replace('/ /', '_', $fileFromForm->getClientOriginalName());
        $nameToSave = ($disk->name === 'public') ? $originalName : preg_split('/\.[A-Za-z0-9]{1,4}/', $fileFromForm->hashName(), -1, PREG_SPLIT_NO_EMPTY)[0];

        if ($mimeType === 'image') {
            return new PublicFile($disk, $nameToSave);
        } else {
            return new LocalFile($disk, $nameToSave, $originalName);
        }

    }

    public function createByDB(int $fileId): File
    {
        $data = $this->filesTDG->findById($fileId);
        $disk = Disk::getDiskByStringDisk($data->disk);
        $securityStatus = SecurityStatus::getSecurityStatusByStringStatus($data->security_status);

        if ($data->original_name === NULL) {
            return new PublicFile($disk, $data->name_to_save, $securityStatus, $data->id);
        } else {
            return new LocalFile($disk, $data->name_to_save, $data->original_name, $securityStatus, $data->id);
        }
    }
}
