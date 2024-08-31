<?php

namespace App\Http\Middleware;

use App\Enums\ViewingStatus;
use App\Services\FilesTDG;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserCanViewTheFile
{
    public function __construct(private FilesTDG $filesTDG){}
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $viewingStatus = ViewingStatus::getViewingStatusByStringStatus($this->filesTDG->getViewingStatus($request->file_id));

        if ($viewingStatus->name === 'private') {
            if (empty($request->cookie('jwt'))) {
                return redirect('privatePassword');
            }
        }

        return $next($request);
    }
}
