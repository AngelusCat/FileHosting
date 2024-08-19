<?php

namespace App\Http\Controllers;

use App\Factories\SimpleDownloadableFileFactory;
use App\Factories\SimpleReturnedFileFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/*
 * TODO: исправить неймспейсы классом с app на App
 */
class FileHosting extends Controller
{
    public function __construct(private readonly SimpleDownloadableFileFactory $simpleDownloadableFileFactory, private readonly SimpleReturnedFileFactory $simpleReturnedFileFactory){}
    public function upload(Request $request): void
    {
        $downloadableFile = $this->simpleDownloadableFileFactory->create($request);
        $fileId = $downloadableFile->upload();
    }

    public function download(int $fileId): StreamedResponse
    {
        $returnedFile = $this->simpleReturnedFileFactory->create($fileId);
        $path = $returnedFile->getPathToDownloadFile();
        $originalName = $returnedFile->getOriginalName();

        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment; filename=' . $originalName
        ];

        /*
         * TODO: Storage::download указывает на диск по умолчанию, т.е. local, значит не нужно давать абсолютный путь
         */

        return Storage::download($path, $originalName, $headers);
    }
}
