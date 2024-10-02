<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidPayload;
use App\Factories\SimpleFactoryFile;
use App\Services\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private Auth $auth){}

    /**
     * @throws InvalidPayload
     */
    public function checkPassword(Request $request, int $fileId): RedirectResponse
    {
        $file = $this->simpleFactoryFile->createByDB($fileId);

        $enteredPassword = ($request->has("password")) ? $request->input("password") : null;

        if ($enteredPassword === null) {
            dd("bad");
        }

        $cookie = $this->auth->authenticate($enteredPassword, $file);

        if ($cookie === null) {
            dd("bad");
        }

        return redirect(route("files.show", ["file" => $file->getId()]))->cookie($cookie);
    }
}
