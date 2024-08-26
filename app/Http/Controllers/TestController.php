<?php

namespace App\Http\Controllers;

use App\newDesign\SimpleFactoryFile;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile){}

    public function upload(Request $request): void
    {
        $file = $this->simpleFactoryFile->createByUploadFile($request->file('file'));
        $content = $request->file('file')->getContent();
        $file->save($content);
    }

    public function download(int $fileId)
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $path = $file->getDownloadPath($fileId);
        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment;'
        ];
        return response()->download($path, null, $headers)->deleteFileAfterSend(true);
    }
}
