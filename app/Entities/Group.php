<?php

namespace App\Entities;

use App\Services\PasswordTDG;

class Group
{
    public function makeFileReadableOnlyByGroup(string $password, File $file): void
    {
        $password = new Password($password, $file, new PasswordTDG("viewing_passwords"));
        $password->install();
    }

    public function makeFileWritableOnlyByGroup(string $password, File $file): void
    {
        $password = new Password($password, $file, new PasswordTDG("modify_passwords"));
        $password->install();
    }
}
