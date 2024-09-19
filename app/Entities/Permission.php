<?php

namespace App\Entities;

use App\Enums\ViewingStatus;
use App\Services\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

abstract class Permission
{
    protected string $permission;
    protected Auth $auth;

    public function __construct(Request $request, File $file)
    {
        $this->auth = App::make(Auth::class);
        $this->determinePermissionValue($request, $file);
    }
    abstract protected function determinePermissionValue(Request $request, File $file): void;

    public function getPermission(): string
    {
        return $this->permission;
    }
}
