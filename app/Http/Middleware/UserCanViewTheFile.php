<?php

namespace App\Http\Middleware;

use App\Enums\ViewingStatus;
use App\Factories\SimpleFactoryFile;
use App\Services\Auth;
use App\Services\FilesTDG;
use App\Services\JWTAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCanViewTheFile
{
    public function __construct(private Auth $auth){}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->auth->isUserAuthenticated($request, "r", $request->file) === false) {
            return redirect(route("viewingPassword", ["file" => $request->file], false));
        }
        return $next($request);
    }
}
