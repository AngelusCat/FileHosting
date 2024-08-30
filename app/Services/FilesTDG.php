<?php

namespace App\Services;

use App\Enums\Disk;
use App\Enums\SecurityStatus;
use Illuminate\Support\Facades\DB;

class FilesTDG
{
    private string $tableName = 'files';

    public function save(Disk $disk, string $nameToSave, SecurityStatus $securityStatus, string $originalName, int $size, string $uploadDate, string $description): int
    {
        return DB::table($this->tableName)->insertGetId([
            'disk' => $disk->name,
            'name_to_save' => $nameToSave,
            'original_name' => $originalName,
            'security_status' => $securityStatus->name,
            'size' => $size,
            'upload_date' => $uploadDate,
            'description' => $description
        ]);
    }

    public function findById(int $id)
    {
        return DB::table($this->tableName)->find($id, ['id', 'disk', 'name_to_save', 'original_name', 'security_status', 'upload_date', 'description', 'size']);
    }
}
