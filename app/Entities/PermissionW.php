<?php

namespace App\Entities;

use App\Entities\Permission;
use App\Enums\ViewingStatus;
use Illuminate\Http\Request;

class PermissionW extends Permission
{
    protected function determinePermissionValue(Request $request, File $file): void
    {
        $this->permission = ($this->auth->isUserAuthenticated($request, "w", $file->getId())) ? "w" : "-";
    }
}
