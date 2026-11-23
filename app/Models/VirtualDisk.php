<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualDisk extends Model
{
    use HasFactory;

    protected $fillable = [
        'disk_name',
        'total_blocks',
        'block_size_bytes',
        'owner_id',
    ];

    // Virtual disk belongs to a user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Virtual disk has many directories
    public function directories(): HasMany
    {
        return $this->hasMany(Directory::class, 'virtual_disk_id');
    }

    // Virtual disk has many root directories
    public function rootDirectories(): HasMany
    {
        return $this->hasMany(Directory::class, 'virtual_disk_id')
            ->whereNull('parent_id');
    }
}




