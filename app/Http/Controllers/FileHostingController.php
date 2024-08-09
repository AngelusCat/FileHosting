<?php

namespace App\Http\Controllers;

use App\Entities\File;
use App\Services\FileSystem;
use Illuminate\Http\Request;

class FileHostingController extends Controller
{
    private FileSystem $fileSystem;
    public function __construct(FileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }
    public function uploadFile(Request $request): void
    {
        dump($request);
        $file = new File($request);
        dump($file);
        $this->fileSystem->save($file);
    }
}
