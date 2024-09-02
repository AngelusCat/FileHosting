<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PrivatePasswordsTDG
{
    private string $tableName = "private_passwords";

    public function save(string $password): int
    {
        return DB::table($this->tableName)->insertGetId(['password' => $password]);
    }
}
