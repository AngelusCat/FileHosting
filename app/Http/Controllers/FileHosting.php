<?php

namespace App\Http\Controllers;

use App\Entities\Password;
use App\Factories\SimpleFactoryFile;
use App\Services\PasswordTDG;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile){}

    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $file = $this->simpleFactoryFile->createByRequestFormData($request);
        $content = $request->file->getContent();
        $file->save($content);
        $fileId = $file->getId();
        if ($file->getViewingStatus()->name === "private") {
            $password = new Password($request->visibilityPassword, $file, new PasswordTDG("viewing_passwords"));
            $password->install();
        }

        if ($request->url() === route("api.files.post")) {
            return response()->json([
                'data' => [
                    'id' => $fileId,
                    'links' => [
                        'self' => "http://file/files/$fileId/content"
                    ]
                ]
            ]);
        } else {
            return redirect("/show/$fileId");
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

    public function show(int $fileId): View
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $downloadLink = "/downloadFile/$fileId";
        return view('show', compact('file', 'downloadLink'));
    }

    public function checkPassword(Request $request, int $fileId)
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $passwordTDG = new PasswordTDG("viewing_passwords");
        $password = new Password($passwordTDG->getPasswordByFileId($file->getId()), $file, $passwordTDG);
        if ($password->isPasswordCorrect($request->password)) {
            //сгенерировать jwt
            return redirect("/show/$fileId")->cookie("jwt", "example", 60);
        } else {
            die('bad pass');
        }
    }
}
