<?php

namespace App\Factories;

use App\Entities\File;
use App\Entities\Password;
use App\Services\PasswordTDG;
use Illuminate\Support\Facades\App;

class SimplePasswordFactory
{
    private array $tdgs;

    public function __construct()
    {
        $this->tdgs = [
            "viewing_passwords" => App::makeWith(PasswordTDG::class, ["tableName" => "viewing_passwords"]),
            "modify_passwords" => App::makeWith(PasswordTDG::class, ["tableName" => "modify_passwords"])
        ];
    }

    public function createViewingPassword(File $file, string $password = ""): Password
    {
        if ($password === "") {
            $password = $this->tdgs["viewing_passwords"]->getPasswordByFileId($file->getId());
        }
        return new Password($password, $file, "viewing_passwords");
    }

    public function createModifyPassword(File $file, string $password = ""): Password
    {
        if ($password === "") {
            $password = $this->tdgs["modify_passwords"]->getPasswordByFileId($file->getId());
        }
        return new Password($password, $file, "modify_passwords");
    }
}
