<?php

namespace App\Http\Controllers;

use App\Entities\Group;
use App\Entities\User;
use App\Enums\ApiRequestStatus;
use App\Exceptions\InvalidPayload;
use App\Exceptions\UploadedFileIsNotValid;
use App\Factories\SimpleFactoryFile;
use App\Factories\SimplePasswordFactory;
use App\Rules\OriginalNameUploadedFileRegex;
use App\Services\Auth;
use App\Services\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Random\RandomException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private Group $group){}

    /**
     * @throws RandomException
     * @throws UploadedFileIsNotValid
     */
    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            "file" => ["required", "between:0.0009,5120", new OriginalNameUploadedFileRegex],
            "description" => ["nullable", "max: 1668", "regex:/^[a-zA-Zа-яёА-ЯЁ0-9\.,;:!?\-—\(\)\"\" ]+$/u"],
            "viewingStatus" => ["required", "in:public,private"],
            "visibilityPassword" => ["required_if:viewingStatus,private", "nullable", "between:8,22", "regex:/[a-zA-Z0-9!@#$%\^&*\(\)\-—_+=;:,\.\/?\\|`~\[\]{}]+/"],
            "modifyPassword" => ["nullable", "between:8,22", "regex:/[a-zA-Z0-9!@#$%\^&*\(\)\-—_+=;:,\.\/?\\|`~\[\]{}]+/"]
        ]);
        $file = $this->simpleFactoryFile->createByRequestFormData($request);
        $content = $request->file->getContent();
        $file->save($content);
        $fileId = $file->getId();

        if ($file->getViewingStatus()->name === "private") {
            $visibilityPassword = $request->visibilityPassword;
            $this->group->makeFileReadableOnlyByGroup($visibilityPassword, $file);
        }

        $modifyPassword = $request->modifyPassword;

        if ($modifyPassword === null && $request->url() === route("api.files.post")) {
            $modifyPassword = bin2hex(random_bytes(8));
        }

        $this->group->makeFileWritableOnlyByGroup($modifyPassword, $file);

        if ($request->url() === route("api.files.post")) {
            return response()->json([
                'status' => ApiRequestStatus::success->name,
                'data' => [
                    'id' => $fileId,
                    'modifyPassword' => $modifyPassword,
                    'links' => [
                        'metadata' => "http://file/api/files/$fileId/metadata",
                        'content' => "http://file/api/files/$fileId/content"
                    ]
                ]
            ]);
        } else {
            return redirect(route("files.show", ["file" => $fileId]));
        }
    }

    public function download(Request $request, int $fileId): BinaryFileResponse|RedirectResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User($request, $file);
        //$user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            return redirect(route("password", ["file" => $fileId]));
        }
        $path = $file->getDownloadPath();
        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment;'
        ];

        return ($file->deleteAfterDownloading()) ? response()->download($path, null, $headers)->deleteFileAfterSend(true)
            : response()->download($path, null, $headers);
    }

    public function show(Request $request, int $fileId): View|RedirectResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User($request, $file);
        //$user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            return redirect(route("password", ["file" => $fileId]));
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

    public function changeMetadata(Request $request, int $fileId): RedirectResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User($request, $file);
        //$user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canWrite() === false) {
            return redirect(route("password", ["file" => $fileId]));
        }
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $request->originalName, -1, PREG_SPLIT_NO_EMPTY)[0];
        $nameToSave = $originalName;
        $description = $request->description;
        $metadata = ($file->getDisk()->name === "public") ? compact("originalName", "nameToSave", "description") : compact("originalName", "description");
        $file->changeMetadata($metadata);
        return redirect(route("files.show", ["file" => $fileId]));
    }
}
