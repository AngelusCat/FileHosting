<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PasswordTDG
{
    private string $tableName;

    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function save(int $fileId, string $password): void
    {
        DB::table($this->tableName)->insert([
            "password" => $password,
            "file_id" => $fileId
        ]);
    }

    public function getPasswordByFileId(int $fileId): string
    {
        return DB::table($this->tableName)->where("file_id", $fileId)->value("password");
    }
}
