<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'virtual_disk_id',
        'directory_id',
        'owner_id',
        'file_name',
        'extension',
        'file_size_bytes',
        'storage_path',
        'allocation_method',
    ];

    public function virtualDisk(): BelongsTo
    {
        return $this->belongsTo(VirtualDisk::class, 'virtual_disk_id');
    }

    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class, 'directory_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the disk blocks allocated to this file.
     */
    public function fileBlocks(): HasMany
    {
        return $this->hasMany(FileBlock::class, 'file_id')
            ->orderBy('block_order');
    }

    /**
     * Get all permissions assigned to this file.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'file_id');
    }
}
