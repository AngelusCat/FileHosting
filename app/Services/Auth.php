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
        if ($request->hasCookie("jwt")) {
            $jwt = $this->authenticator->getJwtFromStringRepresentation($request->cookie("jwt"));
            $payload = $jwt->getDecoratedPayload();
            if ($this->authenticator->validateJWT($jwt) && $payload["file_id"] === $fileId) {
                return $permission === "r" || $permission === "w" && $payload["permissions"] === "all";
            }
        }
        return false;
    }

    /**
     * @throws InvalidPayload
     */
    public function authenticate(string $enteredPassword, File $file): ?Cookie
    {
        $rPassword = $this->simplePasswordFactory->createViewingPassword($file);
        $wPassword = $this->simplePasswordFactory->createModifyPassword($file);

        if ($rPassword->isPasswordCorrect($enteredPassword) || $wPassword->isPasswordCorrect($enteredPassword)) {
            $permissions = ($rPassword->isPasswordCorrect($enteredPassword)) ? "readonly" : "all";
            $payload = json_encode([
                "file_id" => $file->getId(),
                "permissions" => $permissions
            ]);
            $jwt = $this->authenticator->createJwt($payload);
            return cookie("jwt", $jwt->getAll(), 10);
        }
        return null;
    }
}
