<?php

namespace App\Http\Controllers;

use App\Entities\Password;
use App\Factories\SimpleFactoryFile;
use App\Services\JWTAuth;
use App\Services\PasswordTDG;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private JWTAuth $jwtAuth){}

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
            return redirect(route("files.show", ["file" => $fileId]));
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
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $file->getOriginalName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $size = $file->getSize();
        $uploadDate = $file->getUploadDate();
        $description = $file->getDescription();
        $securityStatus = $file->getSecurityStatus()->value;
        $downloadLink = route("files.download", ["file" => $fileId]);
        $csrfToken = csrf_token();
        return view('showEditDelete', compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus', 'downloadLink', 'csrfToken', 'fileId'));
    }

    public function checkPassword(Request $request, int $fileId)
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $passwordTDG = new PasswordTDG("viewing_passwords");
        $password = new Password($passwordTDG->getPasswordByFileId($file->getId()), $file, $passwordTDG);
        if ($password->isPasswordCorrect($request->password)) {
            $payload = json_encode([
                "file_id" => $file->getId(),
            ]);
            $jwt = $this->jwtAuth->createJWT($payload);
            return redirect(route("files.show", ["file" => $file->getId()]))->cookie("jwt", $jwt->getAll(), 1);
        } else {
            die('bad pass');
        }
    }

    public function changeMetadata(Request $request, int $fileId)
    {
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $request->originalName, -1, PREG_SPLIT_NO_EMPTY)[0];
        $nameToSave = $originalName;
        $description = $request->description;
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $metadata = ($file->getDisk()->name === "public") ? compact("originalName", "nameToSave", "description") : compact("originalName", "description");
        $file->changeMetadata($metadata);
    }
}
