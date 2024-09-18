<?php

namespace App\Services;

use App\Exceptions\InvalidPayload;
use Illuminate\Http\Request;

class Auth
{
    private JWTAuth $authenticator;

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
}
