<?php

namespace App\Services;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use Illuminate\Support\Facades\DB;

class FilesTDG
{
    private string $tableName = 'files';

    public function save(Disk $disk, string $nameToSave, string $originalName = "", SecurityStatus $securityStatus = NULL): int
    {
        return DB::table($this->tableName)->insertGetId([
            'disk' => $disk->name,
            'name_to_save' => $nameToSave,
            'original_name' => $originalName,
            'security_status' => ($securityStatus === NULL) ? NULL : $securityStatus->name
        ]);
    }

    public function findById(int $id)
    {
        return DB::table($this->tableName)->find($id, ['disk', 'name_to_save', 'original_name']);
    }
}
