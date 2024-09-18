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
    private array $permissions;
/*
 * Для public всегда r
 * Для private всегда r, если пользователь доказал свою принадлежность к группе наличием валидного ключа, иначе -
 * Для public всегда w, если пользователь доказал принадлежность к группе наличием валидного ключа, иначе -
 * Для private всегда w, если пользователь доказал принадлежность к группе наличием валидного ключа, иначе -
 */

    public function setPermissionsRelativeToCurrentFile(Request $request, File $file): void
    {
        $this->permissions["R"] = new PermissionR($request, $file);
        $this->permissions["W"] = new PermissionW($request, $file);
    }

    private function getR(): string
    {
        return $this->permissions["R"]->getPermission();
    }

    private function getW(): string
    {
        return $this->permissions["W"]->getPermission();
    }

    public function canRead(): bool
    {
        return $this->getR() === "r";
    }

    public function canWrite(): bool
    {
        return $this->getW() === "w";
    }
}
