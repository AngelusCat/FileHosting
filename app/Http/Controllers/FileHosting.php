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
use OpenApi\Annotations as OA;

class FileHosting extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private Group $group, private JsonResponseHelper $jsonResponseHelper){}

    /**
     * @OA\PathItem(
     *      path="/files"
     *      @OA\Post(
     *          summary="Загрузить файл на сервер.",
     *          operationId="uploadFile",
     *          @OA\RequestBody(
     *              required=true,
     *              @OA\MediaType(
     *                  mediaType="multipart/form-data",
     *                  @OA\Schema(
     *                      type="object",
     *                      properties={
     *                          @OA\Property(
     *                              property="file",
     *                              type="string",
     *                              description="Содержимое загружаемого файла на сервер. Размер файла должен быть от 1кб до 5мб.",
     *                              format="binary",
     *                              required=true
     *                          ),
     *                          @OA\Property(
     *                              property="description",
     *                              type="string",
     *                              description="Описание что из себя представляет загружаемый файл.",
     *                              required=false,
     *                              maxLength=1668,
     *                              pattern="^[a-zA-Zа-яёА-ЯЁ0-9\.,;:!?\-—\(\)\"\" ]+$"
     *                          ),
     *                          @OA\Property(
     *                              property="viewingStatus",
     *                              type="string",
     *                              description="Статус видимости файла. Файл может быть публичным (любой пользователь может перейти по ссылке и посмотреть его метаданные) и приватным (метаданные файла может смотреть только тот, у кого есть права на чтение).",
     *                              required=true,
     *                              example="public",
     *                              enum={"public", "private"}
     *                          ),
     *                          @OA\Property(
     *                              property="visibilityPassword",
     *                              type="string",
     *                              required=false,
     *                              description="Этот пароль дает только право на чтение. Его нужно передавать только в случае, если viewingStatus=private.",
     *                              minLength=8,
     *                              maxLength=22,
     *                              pattern="[a-zA-Z0-9!@#$%\^&*\(\)\-—_+=;:,\.\/?\\|`~\[\]{}]+"
     *                          )
     *                      }
     *                  )
     *              )
     *          ),
     *          @OA\Response(
     *              response="200",
     *              description="Файл загружен на сервер",
     *              content={
     *                  @OA\MediaType(
     *                      mediaType="application/json",
     *                      @OA\Schema(
     *                          type="object",
     *                          properties={
     *                              @OA\Property(
     *                                  property="status",
     *                                  ref="#/components/schemas/Status"
     *                              ),
     *                              @OA\Property(
     *                                  property="data",
     *                                  type="object",
     *                                  description="Полезная нагрузка ответа.",
     *                                  properties={
     *                                      @OA\Property(
     *                                          property="modifyPassword",
     *                                          type="string",
     *                                          description="Этот пароль дает право на чтение и на запись (изменение метаданных файла)."
     *                                      ),
     *                                      @OA\Property(
     *                                          property="links",
     *                                          type="object",
     *                                          description="HATEOAS",
     *                                          properties={
     *                                              @OA\Property(
     *                                                  property="metadata",
     *                                                  type="string",
     *                                                  description="URL, чтобы получить метаданные загруженного файла."
     *                                              ),
     *                                              @OA\Property(
     *                                                  property="content",
     *                                                  ref="#/components/schemas/HATEOAS_Content"
     *                                              )
     *                                          }
     *                                      )
     *                                  }
     *                              )
     *                          }
     *                      )
     *                  )
     *              }
     *          )
     *      )
     *  )
     * /
     */

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

    /**
     * @OA\PathItem(
     *     path="/files/{id}/metadata",
     *     @OA\Get(
     *         summary="Получить метаданные файла: имя, размер (в байтах), дату загрузки, описание, статус проверки антивирусом",
     *         operationId="getFileMetadata",
     *         @OA\Parameter(
     *             name="id",
     *             in="path",
     *             description="ID загруженного файла.",
     *             required=true,
     *             allowEmptyValue=false,
     *             @OA\Schema(
     *                 type="integer";
     *             )
     *         ),
     *         @OA\Response(
     *             response:"200",
     *             description="Метаданные загруженного файла.",
     *             content={
     *                 @OA\MediaType(
     *                     mediaType="application/json",
     *                     @OA\Schema(
     *                         type="object",
     *                         properties={
     *                             @OA\Property(
     *                                 property="status",
     *                                 ref="#/components/schemas/Status"
     *                             ),
     *                             @OA\Property(
     *                                 property="data",
     *                                 type="object",
     *                                 description="Полезная нагрузка ответа.",
     *                                 properties={
     *                                     @OA\Property(
     *                                         property="name",
     *                                         type="string",
     *                                         description="Название файла."
     *                                     ),
     *                                     @OA\Property(
     *                                         property="size",
     *                                         type="integer",
     *                                         description="Размер файла в байтах."
     *                                     ),
     *                                     @OA\Property(
     *                                         property="uploadDate",
     *                                         type="string",
     *                                         format="date-time",
     *                                         description="Дата и время загрузки файла."
     *                                     ),
     *                                     @OA\Property(
     *                                         property="description",
     *                                         type="string",
     *                                         description="Описание содержимого файла."
     *                                     ),
     *                                     @OA\Property(
     *                                         property="securityStatus",
     *                                         type="string",
     *                                         description="Статус проверки файла антивирусом.",
     *                                         enum={"безопасный", "подозрительный", "вредоносный", "не проверен"}
     *                                     ),
     *                                     @OA\Property(
     *                                         property="links",
     *                                         type="object",
     *                                         description="HATEOAS",
     *                                         properties={
     *                                             @OA\Property(
     *                                                 property="content",
     *                                                 ref="#/components/schemas/HATEOAS_Content"
     *                                             ),
     *                                             @OA\Property(
     *                                                 //
     *                                             )
     *                                         }
     *                                     )
     *                                 }
     *                             )
     *                         }
     *                     )
     *                 )
     *             }
     *         )
     *     )
     * )
     */

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
