<?php

namespace App\Services\DB;

use Illuminate\Support\Facades\DB;

class PrivateFilePasswordsDB
{
    private string $tableName = 'privateFilePasswords';
    public function save(int $fileId, string $password): void
    {
        DB::table($this->tableName)->insert([
            'file_id' => $fileId,
            'password' => $password
        ]);
    }
}
