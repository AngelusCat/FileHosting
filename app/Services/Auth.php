<?php

namespace App\Services;

use App\Entities\File;
use App\Exceptions\InvalidPayload;
use App\Factories\SimplePasswordFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class Auth
{
    private JWTAuth $authenticator;
    private SimplePasswordFactory $simplePasswordFactory;

    public function __construct()
    {
        $this->authenticator = new JWTAuth();
        $this->simplePasswordFactory = new SimplePasswordFactory();
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
    public function authenticate(string $permission, string $enteredPassword, File $file): ?Cookie
    {
        $password = ($permission === "r") ? $this->simplePasswordFactory->createViewingPassword($file) : $this->simplePasswordFactory->createModifyPassword($file);

        if ($password->isPasswordCorrect($enteredPassword)) {
            $payload = json_encode([
                "file_id" => $file->getId(),
            ]);
            $jwt = $this->authenticator->createJWT($payload);
            $cookieName = "jwt_" . $permission;
            return cookie($cookieName, $jwt->getAll(), 1);
        }
        return null;
    }
}
