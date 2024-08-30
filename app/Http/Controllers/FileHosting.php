<?php

namespace App\Http\Controllers;

use App\Factories\SimpleFactoryFile;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile){}

    public function upload(Request $request)
    {
        $fileFromForm = $request->file;
        $file = $this->simpleFactoryFile->createByUploadFile($fileFromForm);
        $content = $fileFromForm->getContent();
        $file->save($content);
        $fileId = $file->getId();

        if ($request->url() === "http://file/api/files") {
            return response()->json([
                'data' => [
                    'id' => $fileId,
                    'links' => [
                        'self' => "http://file/files/$fileId/content"
                    ]
                ]
            ]);
        }
    }

    public function download(int $fileId): BinaryFileResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $path = $file->getDownloadPath();
        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment;'
        ];

        return ($file->deleteAfterDownloading()) ? response()->download($path, null, $headers)->deleteFileAfterSend(true)
            : response()->download($path, null, $headers);
    }
}
