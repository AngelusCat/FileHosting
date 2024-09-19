<?php

namespace App\Entities;

use App\Services\PasswordTDG;
use Illuminate\Support\Facades\App;

class Password
{
    private File $file;
    private string $password;
    private PasswordTDG $passwordTDG;

    public function __construct(string $password, File $file, string $tableName)
    {
        $this->password = $password;
        $this->file = $file;
        $this->passwordTDG = App::makeWith(PasswordTDG::class, ['tableName' => $tableName]);
    }

    public function install(): void
    {
        $this->passwordTDG->save($this->file->getId(), password_hash($this->password, PASSWORD_DEFAULT));
    }

    public function isPasswordCorrect(string $enteredPassword): bool
    {
        return password_verify($enteredPassword, $this->password);
    }
}
