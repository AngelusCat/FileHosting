<?php

namespace App\Http\Controllers;

use App\Enums\Disk;
use app\Test\DownloadableFile;
use app\Test\FilesTDG;
use app\Test\SimpleDownloadableFileFactory;
use app\Test\SimpleReturnedFileFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/*
 * TODO: исправить неймспейсы классом с app на App
 */
class TestController extends Controller
{
    public function __construct(private readonly FilesTDG $filesTDG, private readonly SimpleDownloadableFileFactory $simpleDownloadableFileFactory, private SimpleReturnedFileFactory $simpleReturnedFileFactory){}
    public function upload(Request $request): void
    {
        $downloadableFile = $this->simpleDownloadableFileFactory->create($request);
        $fileId = $downloadableFile->upload();
    }

    public function unload(int $fileId): StreamedResponse
    {
        $returnedFile = $this->simpleReturnedFileFactory->create($fileId);
        $path = $returnedFile->getPathToDownloadFile();
        $originalName = $returnedFile->getOriginalName();

        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment; filename=' . $originalName
        ];

        return Storage::download($path, $originalName, $headers);
    }
}
