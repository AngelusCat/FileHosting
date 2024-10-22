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
use App\Services\JsonResponseHelper;
use App\Services\JWTAuth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Random\RandomException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private Group $group, private JsonResponseHelper $jsonResponseHelper){}

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
        $isThisApiRequest = $request->url() === route("api.files.upload");
        $file = $this->simpleFactoryFile->createByRequestFormData($request);
        $content = $request->file->getContent();
        $file->save($content);
        $fileId = $file->getId();

        if ($file->getViewingStatus()->name === "private") {
            $passwordR = $request->visibilityPassword;
            $this->group->makeFileReadableOnlyByGroup($passwordR, $file);
        }
        $passwordW = $request->modifyPassword;
        if ($passwordW === null && $isThisApiRequest) {
            $passwordW = bin2hex(random_bytes(8));
        }
        $this->group->makeFileWritableOnlyByGroup($passwordW, $file);

        return ($isThisApiRequest) ? $this->jsonResponseHelper->getSuccessfulResponseForUpload($passwordW, $fileId) :
            redirect(route("files.show", ["file" => $fileId]));
    }

    public function download(Request $request, int $fileId): BinaryFileResponse|RedirectResponse|JsonResponse
    {
        $isThisApiRequest = $request->url() === route("api.files.content", ['id' => $fileId]);
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            return $this->sendAuthenticationResponse($isThisApiRequest, $fileId);
        }
        $path = $file->getDownloadPath();

        if ($isThisApiRequest) {
            return $this->jsonResponseHelper->getSuccessfulResponseForDownload($fileId, $path);
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
        $isThisApiRequest = $request->url() === route("api.files.metadata", ['id' => $fileId]);
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canRead() === false) {
            return $this->sendAuthenticationResponse($isThisApiRequest, $fileId);
        }
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $file->getOriginalName(), -1, PREG_SPLIT_NO_EMPTY)[0];
        $size = $file->getSize();
        $uploadDate = $file->getUploadDate();
        $description = $file->getDescription();
        $securityStatus = $file->getSecurityStatus()->value;
        $downloadLink = route("files.download", ["file" => $fileId]);
        $csrfToken = csrf_token();
        return ($isThisApiRequest) ? $this->jsonResponseHelper->getSuccessfulResponseForShow($file) :
            view('showEditDelete', compact('originalName', 'size', 'uploadDate', 'description', 'securityStatus', 'downloadLink', 'csrfToken', 'fileId'));
    }

    public function changeMetadata(Request $request, int $fileId): RedirectResponse|JsonResponse
    {
        $isThisApiRequest = $request->url() === route("api.files.update", ['id' => $fileId]);
        $file = $this->simpleFactoryFile->createByDB($fileId);
        $user = new User();
        $user->setPermissionsRelativeToCurrentFile($request, $file);
        if ($user->canWrite() === false) {
            return $this->sendAuthenticationResponse($isThisApiRequest, $fileId);
        }
        $originalName = preg_split('/\.[A-Za-z0-9]{1,4}/', $request->name, -1, PREG_SPLIT_NO_EMPTY)[0];
        $nameToSave = $originalName;
        $description = $request->description;
        $metadata = ($file->getDisk()->name === "public") ? compact("originalName", "nameToSave", "description") : compact("originalName", "description");
        $file->changeMetadata($metadata);
        return ($isThisApiRequest) ? $this->jsonResponseHelper->getSuccessfulResponseForChangeMetadata($fileId) : redirect(route("files.show", ["file" => $fileId]));
    }

    private function sendAuthenticationResponse(bool $isThisApiRequest, int $fileId): JsonResponse|RedirectResponse
    {
        return ($isThisApiRequest) ? $this->jsonResponseHelper->getResponseUserIsNotAuthorized($fileId) : redirect(route("password", ["file" => $fileId]));
    }
}
