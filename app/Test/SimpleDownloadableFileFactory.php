<?php

namespace app\Test;

use App\Enums\Disk;
use Illuminate\Http\Request;

class SimpleDownloadableFileFactory
{
    public function create(Request $request): DownloadableFile
    {
        $fileFromForm = $request->file('file');

        $mimeType = [];
        preg_match('/image/', $fileFromForm->getMimeType(), $mimeType);
        $mimeType = $mimeType[0] ?? '';

        $disk = ($mimeType === 'image') ? Disk::public : Disk::local;
        $originalName = preg_replace('/ /', '_', $fileFromForm->getClientOriginalName());
        $nameToSave = ($disk->name === 'public') ? $originalName : preg_split('/\.[A-Za-z0-9]{1,4}$/', $fileFromForm->hashName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $content = $fileFromForm->getContent();

        return new DownloadableFile($disk, $nameToSave, $originalName, $content);
    }
}
