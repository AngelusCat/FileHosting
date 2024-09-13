<?php

namespace App\Entities;

use App\Enums\ViewingStatus;
use App\Services\Auth;
use Illuminate\Http\Request;

/**
 * Человек заходит на сайт -> ему устанавливаются права
 * Если человек аутентифицирован, то ему ставятся rw
 * Иначе ему ставится --
 * Если у пользователя -, то отправить его заполнять пароль
 * Пароль верный => поменять права на r или w
 * Пароль неверный => оставить права теми же
 */

class User
{
    private string $permissions;
    private Auth $auth;

/*
 * Для public всегда r
 * Для private всегда r, если пользователь доказал свою принадлежность к группе наличием валидного ключа, иначе -
 * Для public всегда w, если пользователь доказал принадлежность к группе наличием валидного ключа, иначе -
 * Для private всегда w, если пользователь доказал принадлежность к группе наличием валидного ключа, иначе -
 */

    public function setPermissionsRelativeToCurrentFile(Request $request, ViewingStatus $viewingStatus, int $fileId): void
    {
        if ($viewingStatus->name === "public") {
            $this->permissions = "r";
        } else {
            $this->permissions = ($this->auth->isUserAuthenticated($request, "r", $fileId)) ? "r" : "-";
        }

        $w = ($this->auth->isUserAuthenticated($request, "w", $fileId)) ? "w" : "-";
        $this->permissions .= $w;
    }
}
