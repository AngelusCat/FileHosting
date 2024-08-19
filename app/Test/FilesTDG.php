<?php

namespace App\Test;

use App\Enums\Disk;
use Illuminate\Support\Facades\DB;

class FilesTDG
{
    private string $tableName = 'files';

    public function save(Disk $disk, string $nameToSave, string $originalName): int
    {
        return DB::table($this->tableName)->insertGetId([
            'disk' => $disk->name,
            'name_to_save' => $nameToSave,
            'original_name' => $originalName
        ]);
    }

    public function findById(int $id)
    {
        return DB::table($this->tableName)->find($id, ['disk', 'name_to_save', 'original_name']);
    }
}
