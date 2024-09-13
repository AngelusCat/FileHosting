<?php

namespace App\Services;

use Illuminate\Http\Request;

class Auth
{
    private JWTAuth $authenticator;

    public function isUserAuthenticated(Request $request): bool
    {
        //
    }
}
