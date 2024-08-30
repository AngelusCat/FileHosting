<?php

namespace App\Services;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use Illuminate\Support\Facades\DB;

class FilesTDG
{
    private string $tableName = 'files';

    public function save(Disk $disk, string $nameToSave, SecurityStatus $securityStatus, string $originalName = NULL): int
    {
        return DB::table($this->tableName)->insertGetId([
            'disk' => $disk->name,
            'name_to_save' => $nameToSave,
            'original_name' => $originalName,
            'security_status' => $securityStatus->name
        ]);
    }

    public function findById(int $id)
    {
        return DB::table($this->tableName)->find($id, ['id', 'disk', 'name_to_save', 'original_name', 'security_status']);
    }
}
