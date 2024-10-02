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
        if ($request->hasCookie("jwt") === false) {
            return false;
        } else {
            $jwt = $this->authenticator->getJwtFromStringRepresentation($request->cookie("jwt"));
            $payload = $jwt->getDecoratedPayload();
            if ($this->authenticator->validateJWT($jwt) && $payload["file_id"] === $fileId) {
                return $permission === "r" || $permission === "w" && $payload["permissions"] === "all";
            }
        }
    }

    /**
     * @throws InvalidPayload
     */
    public function authenticate(string $permission, string $enteredPassword, File $file): ?Cookie
    {
        if ($permission === "r") {
            $permissions = "readonly";
            $password = $this->simplePasswordFactory->createViewingPassword($file);
        } else if ($permission === "w") {
            $permissions = "all";
            $password = $this->simplePasswordFactory->createModifyPassword($file);
        }

        if ($password->isPasswordCorrect($enteredPassword)) {
            $payload = json_encode([
                "file_id" => $file->getId(),
                "permissions" => $permissions
            ]);
            $jwt = $this->authenticator->createJwt($payload);
            return cookie("jwt", $jwt->getAll(), 10);
        } else {
            return null;
        }
    }
}
