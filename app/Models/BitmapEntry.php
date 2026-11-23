<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitmapEntry extends Model
{
    protected $fillable = [
        'disk_block_id',
        'bit_value',
    ];

    protected $casts = [
        'bit_value' => 'boolean',
    ];

    public function diskBlock(): BelongsTo
    {
        return $this->belongsTo(DiskBlock::class, 'disk_block_id');
    }
}


