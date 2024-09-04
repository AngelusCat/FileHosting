<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class VisibilityPasswordsTDG
{
    private string $tableName = "visibility_passwords";

    public function save(string $password, int $fileId): void
    {
        DB::table($this->tableName)->insert([
            "password" => $password,
            "file_id" => $fileId
        ]);
    }

    public function getPasswordsByFileId(int $fileId): string
    {
        return DB::table($this->tableName)->where("file_id", $fileId)->value("password");
    }
}
