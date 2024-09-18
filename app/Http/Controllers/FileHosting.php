<?php

namespace App\Http\Controllers;

use App\Entities\Group;
use App\Entities\Password;
use App\Entities\User;
use App\Exceptions\InvalidPayload;
use App\Exceptions\UploadedFileIsNotValid;
use App\Factories\SimpleFactoryFile;
use App\Services\JWTAuth;
use App\Services\PasswordTDG;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Random\RandomException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use function PHPUnit\Framework\isNull;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private JWTAuth $jwtAuth, private Group $group){}

    /**
     * @throws RandomException
     * @throws UploadedFileIsNotValid
     */
    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $file = $this->simpleFactoryFile->createByRequestFormData($request);
        $content = $request->file->getContent();
        $file->save($content);
        $fileId = $file->getId();

        if ($file->getViewingStatus()->name === "private") {
            $visibilityPassword = $request->visibilityPassword;
            $this->group->makeFileReadableOnlyByGroup($visibilityPassword, $file);
        }

        $modifyPassword = bin2hex(random_bytes(5));
        $this->group->makeFileWritableOnlyByGroup($modifyPassword, $file);

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

    public function download(Request $request, int $fileId): BinaryFileResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            die("Перенаправление на страницу логина");
        }
        $path = $file->getDownloadPath();
        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment;'
        ];

        return ($file->deleteAfterDownloading()) ? response()->download($path, null, $headers)->deleteFileAfterSend(true)
            : response()->download($path, null, $headers);
    }

    public function show(Request $request, int $fileId): View
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            die("Перенаправление на страницу логина");
        }
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $file->getOriginalName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $size = $file->getSize();
        $uploadDate = $file->getUploadDate();
        $description = $file->getDescription();
        $securityStatus = $file->getSecurityStatus()->value;
        $downloadLink = route("files.download", ["file" => $fileId]);
        $csrfToken = csrf_token();
        return view('showEditDelete', compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus', 'downloadLink', 'csrfToken', 'fileId'));
    }

    /**
     * @throws InvalidPayload
     */
    public function checkPassword(Request $request, int $fileId)
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);

        if ($request->has("passwordR")) {
            $passwordTDG = new PasswordTDG("viewing_passwords");
            $enteredPassword = $request->passwordR;
            $cookieName = "jwt_r";
        } elseif ($request->has("passwordW")) {
            $passwordTDG = new PasswordTDG("modify_passwords");
            $enteredPassword = $request->passwordW;
            $cookieName = "jwt_w";
        }
        $password = new Password($passwordTDG->getPasswordByFileId($file->getId()), $file, $passwordTDG);
        if ($password->isPasswordCorrect($enteredPassword)) {
            $payload = json_encode([
                "file_id" => $file->getId(),
            ]);
            $jwt = $this->jwtAuth->createJWT($payload);
            return redirect(route("files.show", ["file" => $file->getId()]))->cookie($cookieName, $jwt->getAll(), 1);
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
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canWrite() === false) {
            die("Перенаправление на страницу логина");
        }
        $metadata = ($file->getDisk()->name === "public") ? compact("originalName", "nameToSave", "description") : compact("originalName", "description");
        $file->changeMetadata($metadata);
    }
}
