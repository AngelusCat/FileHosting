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

        $file = $this->simpleFactoryFile->createByDB($fileId);

        $enteredPassword = $request->input("password");

        $cookie = $this->auth->authenticate($enteredPassword, $file);

        if ($cookie === null) {
            if ($request->url() === route("api.auth", ['id' => $fileId])) {
                return response()->json([
                    'status' => ApiRequestStatus::fail->name,
                    'message' => 'Пароль неверный'
                ]);
            }
            return back()->withErrors(["Пароль неверный."]);
        }

        if ($request->url() === route("api.auth", ['id' => $fileId])) {
            return response()->json([
                'status' => ApiRequestStatus::success->name
            ])->cookie($cookie);
        } else {
            return redirect(route("files.show", ["file" => $file->getId()]))->cookie($cookie);
        }
    }
}
