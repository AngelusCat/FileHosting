<?php

namespace App\Entities;

use App\Entities\Permission;
use App\Enums\ViewingStatus;
use Illuminate\Http\Request;

class PermissionR extends Permission
{
    protected function determinePermissionValue(Request $request, File $file): void
    {
        if ($file->getViewingStatus()->name === "public") {
            $this->permission = "r";
        } else {
            $this->permission = ($this->auth->isUserAuthenticated($request, "r", $file->getId())) ? "r" : "-";
        }
    }
}
