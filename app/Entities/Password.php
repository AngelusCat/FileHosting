<?php

namespace App\Entities;

use App\Services\PasswordTDG;

class Password
{
    private File $file;
    private string $password;
    private PasswordTDG $passwordTDG;

    public function __construct(string $password, File $file, PasswordTDG $passwordTDG)
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->file = $file;
        $this->passwordTDG = $passwordTDG;
    }

    public function install(): void
    {
        $this->passwordTDG->save($this->file->getId(), $this->password);
    }

    public function isPasswordCorrect(string $enteredPassword): bool
    {
        $password = $this->passwordTDG->getPasswordByFileId($this->file->getId());
        return password_verify($enteredPassword, $password);
    }
}
