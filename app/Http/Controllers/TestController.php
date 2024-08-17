<?php

namespace App\Http\Controllers;

use App\Enums\Disk;
use app\Test\DownloadableFile;
use app\Test\FilesTDG;
use app\Test\SimpleDownloadableFileFactory;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct(private readonly FilesTDG $filesTDG, private readonly SimpleDownloadableFileFactory $simpleDownloadableFileFactory){}
    public function upload(Request $request)
    {
        $downloadableFile = $this->simpleDownloadableFileFactory->create($request);
        $fileId = $downloadableFile->upload();
    }

    public function unload(int $fileId)
    {
        $file = $this->filesTDG->findById($id);
    }
}
