<?php

namespace App\Http\Controllers;

use App\Enums\ApiRequestStatus;
use App\Exceptions\InvalidPayload;
use App\Factories\SimpleFactoryFile;
use App\Services\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private Auth $auth){}

    /**
     * @OA\PathItem(
     *     path="/auth/files/{id}",
     *     @OA\Post(
     *         summary="Аутентифицироваться и авторизоваться.",
     *         operationId="auth",
     *         @OA\Parameter(ref="#/components/parameters/fileId"),
     *         @OA\RequestBody(
     *             required=true,
     *             @OA\MediaType(
     *                 mediaType="multipart/form-data",
     *                 @OA\Schema(
     *                     type="object",
     *                     properties={
     *                         @OA\Property(
     *                             property="password",
     *                             type="string",
     *                             required=true,
     *                             nullable=false,
     *                             description="Если viewingStatus = private и нужно дать только права на чтение, то ввести visibilityPassword; дать права на чтение и запись - modifyPassword.",
     *                             minLength=8,
     *                             maxLength=22,
     *                             pattern="[a-zA-Z0-9!@#$%\^&*\(\)\-—_+=;:,\.\/?\\|`~\[\]{}]+"
     *                         )
     *                     }
     *                 )
     *             )
     *         ),
     *         @OA\Response(
     *             response="200",
     *             description="Пользователь аутентифицирован и авторизован."
     *             @OA\JsonContent(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(
     *                         property="status",
     *                         ref="#/components/schemas/Status"
     *                     )
     *                 }
     *             )
     *         ),
     *         @OA\Response(
     *             response="422",
     *             ref="#/components/responses/ValidationErrorResponse"
     *         ),
     *         @OA\Response(
     *             response="401",
     *             ref="#/components/responses/UserIsNotAuthorized"
     *         )
     *     )
     * )
     */

    /**
     * @throws InvalidPayload
     */
    public function checkPassword(Request $request, int $fileId): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            "password" => "filled|between:8,22",
        ], [
            "password.filled" => "Пароль не может быть пустым.",
            "password.between" => "Пароль должен содержать от :min до :max символов.",
        ], [
            ":min" => 8,
            ":max" => 22
        ]);

        $isThisApiRequest = $request->url() === route("api.auth", ['id' => $fileId]);

        $file = $this->simpleFactoryFile->createByDB($fileId);

        $enteredPassword = $request->input("password");

        $cookie = $this->auth->authenticate($enteredPassword, $file);

        if ($cookie === null) {
            if ($isThisApiRequest) {
                return response()->json([
                    'status' => ApiRequestStatus::fail->name,
                    'message' => 'Пароль неверный'
                ], 401);
            }
            return back()->withErrors(["Пароль неверный."]);
        }

        if ($isThisApiRequest) {
            return response()->json([
                'status' => ApiRequestStatus::success->name
            ])->cookie($cookie);
        } else {
            return redirect(route("files.show", ["file" => $file->getId()]))->cookie($cookie);
        }
    }
}
