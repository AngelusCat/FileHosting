<?php

namespace App\Http\Controllers;

use App\Entities\File;
use App\Services\DB\FileDB;
use App\Services\FileSystem;
use Illuminate\Http\Request;

class FileHostingController extends Controller
{
    private FileSystem $fileSystem;
    private FileDB $fileDB;

    public function __construct(FileSystem $fileSystem, FileDB $fileDB)
    {
        $this->fileSystem = $fileSystem;
        $this->fileDB = $fileDB;
    }
    public function uploadFile(Request $request): void
    {
        dump($request);

        $file = new File($request);
        dump($file);

        $this->fileSystem->save($file);

        $fileId = $this->fileDB->save($file);

        $file->setId($fileId);
        $file->setAccessRights();
    }
}
