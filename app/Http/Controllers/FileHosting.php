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

        if ($request->url() === route("api.files.upload")) {
            return response()->json([
                'status' => ApiRequestStatus::success->name,
                'data' => [
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

    public function download(Request $request, int $fileId): BinaryFileResponse|RedirectResponse|JsonResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            if ($request->url() === route("api.files.content", ['id' => $fileId])) {
                return response()->json([
                    'status' => ApiRequestStatus::fail->name,
                    'data' => [
                        "message" => "Ошибка авторизации",
                        "link" => [
                            "auth" => "http://file/api/auth/$fileId"
                        ]
                    ]
                ]);
            }
            return redirect(route("password", ["file" => $fileId]));
        }
        $path = $file->getDownloadPath();

        if ($request->url() === route("api.files.content", ['id' => $fileId])) {
            return response()->json([
                'status' => ApiRequestStatus::success->name,
                'data' => [
                    'content' => mb_convert_encoding(file_get_contents($path), 'utf8', 'UTF-8'),
                    'link' => [
                        'metadata' => "http://file/api/files/$fileId/metadata"
                    ]
                ]
            ]);
        }

        $headers = [
            'Content-Security-Policy' => "default-src 'none'; script-src 'none'; form-action 'none'",
            'Content-Disposition' => 'attachment;'
        ];

        return ($file->deleteAfterDownloading()) ? response()->download($path, null, $headers)->deleteFileAfterSend(true)
            : response()->download($path, null, $headers);
    }

    public function show(Request $request, int $fileId): View|RedirectResponse|JsonResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            if ($request->url() === route("api.files.metadata", ['id' => $fileId])) {
                return response()->json([
                    'status' => ApiRequestStatus::fail->name,
                    'data' => [
                        "message" => "Ошибка авторизации",
                        "link" => [
                            "auth" => "http://file/api/auth/$fileId"
                        ]
                    ]
                ]);
            }
            return redirect(route("password", ["file" => $fileId]));
        }
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $file->getOriginalName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $size = $file->getSize();
        $uploadDate = $file->getUploadDate();
        $description = $file->getDescription();
        $securityStatus = $file->getSecurityStatus()->value;
        $downloadLink = route("files.download", ["file" => $fileId]);
        $csrfToken = csrf_token();
        if ($request->url() === route("api.files.metadata", ['id' => $fileId])) {
            return response()->json([
                'status' => ApiRequestStatus::success->name,
                'data' => compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus') + ['link' => [
                    'content' => "http://file/api/files/$fileId/content",
                        'update' => "http://file/api/files/$fileId"
                    ]
                    ]
            ]);
        } else {
            return view('showEditDelete', compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus', 'downloadLink', 'csrfToken', 'fileId'));
        }
    }

    public function changeMetadata(Request $request, int $fileId): RedirectResponse|JsonResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canWrite() === false) {
            if ($request->url() === route("api.files.update", ['id' => $fileId])) {
                return response()->json([
                    'status' => ApiRequestStatus::fail->name,
                    'data' => [
                        "message" => "Ошибка авторизации",
                        "link" => [
                            "auth" => "http://file/api/auth/$fileId"
                        ]
                    ]
                ]);
            }
            return redirect(route("password", ["file" => $fileId]));
        }
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $request->originalName, -1, PREG_SPLIT_NO_EMPTY)[0];
        $nameToSave = $originalName;
        $description = $request->description;
        $metadata = ($file->getDisk()->name === "public") ? compact("originalName", "nameToSave", "description") : compact("originalName", "description");
        $file->changeMetadata($metadata);
        if ($request->url() === route("api.files.update", ['id' => $fileId])) {
            return response()->json([
                'status' => ApiRequestStatus::success->name,
                'data' => [
                    'link' => [
                        'metadata' => "http://file/api/files/$fileId/metadata"
                    ]
                ]
            ]);
        } else {
            return redirect(route("files.show", ["file" => $fileId]));
        }
    }
}
