<?php

namespace App\Factories;

use App\Enums\Disk;
use App\Entities\File;
use App\Entities\LocalFile;
use App\Entities\PublicFile;
use App\Enums\SecurityStatus;
use App\Services\FilesTDG;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SimpleFactoryFile
{
    public function __construct(private FilesTDG $filesTDG){}
    public function createByRequestFormData(Request $request): File
    {
        $fileFromForm = $request->file;
        $mimeType = [];
        preg_match('/image/', $fileFromForm->getMimeType(), $mimeType);
        $mimeType = $mimeType[0] ?? '';

        $disk = ($mimeType === 'image') ? Disk::public : Disk::local;
        $originalName = preg_replace('/ /', '_', $fileFromForm->getClientOriginalName());
        $nameToSave = ($disk->name === 'public') ? $originalName : preg_split('/\.[A-Za-z0-9]{1,4}/', $fileFromForm->hashName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $size = $fileFromForm->getSize();
        $uploadDate = now();
        $description = $request->description;

        if ($mimeType === 'image') {
            return new PublicFile($disk, $nameToSave, $originalName, $size, $uploadDate, $description);
        } else {
            return new LocalFile($disk, $nameToSave, $originalName, $size, $uploadDate, $description);
        }
    }

    public function createByDB(int $fileId): File
    {
        $data = $this->filesTDG->findById($fileId);
        $id = $data->id;
        $disk = Disk::getDiskByStringDisk($data->disk);
        $nameToSave = $data->name_to_save;
        $originalName = $data->original_name;
        $size = $data->size;
        $uploadDate = new Carbon($data->upload_date);
        $description = $data->description;
        $securityStatus = SecurityStatus::getSecurityStatusByStringStatus($data->security_status);

        if ($disk->name === 'public') {
            return new PublicFile($disk, $nameToSave, $originalName, $size, $uploadDate, $description, $securityStatus, $id);
        } else {
            return new LocalFile($disk, $nameToSave, $originalName, $size, $uploadDate, $description, $securityStatus, $id);
        }
    }
}
