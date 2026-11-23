<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileBlock extends Model
{
    protected $fillable = [
        'file_id',
        'disk_block_id',
        'block_order',
        'is_index_block',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function diskBlock(): BelongsTo
    {
        return $this->belongsTo(DiskBlock::class, 'disk_block_id');
    }
}

