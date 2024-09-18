<?php

namespace App\Services;

use App\Entities\File;
use App\Entities\JWT;
use App\Entities\Password;
use App\Exceptions\InvalidPayload;
use Illuminate\Http\Request;

class Auth
{
    private JWTAuth $authenticator;

    public function __construct()
    {
        $this->authenticator = new JWTAuth();
    }

    public function isUserAuthenticated(Request $request, string $permission, int $fileId): bool
    {
        $cookieName = "jwt_" . $permission;
        if ($request->hasCookie($cookieName) === false) {
            return false;
        } else {
            $jwt = $this->authenticator->getJwtFromStringRepresentation($request->cookie($cookieName));
            $fileIdFromPayload = $jwt->getDecoratedPayload()["file_id"];
            return ($this->authenticator->validateJWT($jwt) && $fileIdFromPayload === $fileId);
        }
    }

    /**
     * @throws InvalidPayload
     */
    public function authenticate(string $permission, string $enteredPassword, File $file): JWT
    {
        $tableName = "passwords_" . mb_strtolower($permission);
        $passwordTDG = new PasswordTDG($tableName);
        $passwordFromDB = $passwordTDG->getPasswordByFileId($file->getId());
        $password = new Password($passwordFromDB, $file, $passwordTDG);
        if ($password->isPasswordCorrect($enteredPassword)) {
            $payload = json_encode([
                "file_id" => $file->getId(),
            ]);
           return $this->authenticator->createJWT($payload);
        }
    }
}
