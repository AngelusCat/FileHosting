<?php

namespace App\Entities;

class User
{
    private string $permissions;

    public function setPermissionsRelativeToCurrentFile()
    {
        /*
         * Для public всегда r
         * Для private всегда r, если пользователь доказал свою принадлежность к группе наличием валидного ключа, иначе -
         * Для public всегда w, если пользователь доказал принадлежнолсть к группе наличием валидного ключа, иначе -
         * Для private всегда w, если пользователь доказал принадлежнолсть к группе наличием валидного ключа, иначе -
         * 
         */
    }
}
