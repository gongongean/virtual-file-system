<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DiskBlock extends Model
{
    protected $fillable = [
        'virtual_disk_id',
        'block_number',
        'block_data',
        'next_block_id',
    ];

    public function virtualDisk(): BelongsTo
    {
        return $this->belongsTo(VirtualDisk::class, 'virtual_disk_id');
    }

    public function fileLink(): HasOne
    {
        return $this->hasOne(FileBlock::class, 'disk_block_id');
    }

    public function bitmapEntry(): HasOne
    {
        return $this->hasOne(BitmapEntry::class, 'disk_block_id');
    }

    public function nextBlock(): BelongsTo
    {
        return $this->belongsTo(DiskBlock::class, 'next_block_id');
    }
}


