<?php

namespace App\Http\Middleware;

use App\Enums\ViewingStatus;
use App\Factories\SimpleFactoryFile;
use App\Services\FilesTDG;
use App\Services\JWTAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCanViewTheFile
{
    public function __construct(private SimpleFactoryFile $simpleFactoryFile, private JWTAuth $jwtAuth){}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $file = $this->simpleFactoryFile->createByDB($request->file_id);
        $viewingStatus = $file->getViewingStatus();

        if ($viewingStatus->name === 'private') {
            if (empty($request->cookie('jwt'))) {
                return redirect($file->getId() . "/privatePassword");
            } else {
                $jwt = $this->jwtAuth->getJwtFromStringRepresentation($request->cookie('jwt'));
                $fileIdFromJWT = $jwt->getDecoratedPayload()["file_id"];
                if ($this->jwtAuth->validateJWT($jwt) === false || $fileIdFromJWT !== $file->getId()) {
                    return redirect($file->getId() . "/privatePassword");
                }
            }
        }
        return $next($request);
    }
}
