<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Directory extends Model
{
    use HasFactory;

    protected $fillable = [
        'virtual_disk_id',
        'owner_id',
        'parent_id',
        'directory_name',
    ];

    // Directory belongs to a virtual disk
    public function virtualDisk(): BelongsTo
    {
        return $this->belongsTo(VirtualDisk::class, 'virtual_disk_id');
    }

    // Directory belongs to a user
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Directory belongs to a parent directory
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Directory::class, 'parent_id');
    }

    // Directory has many subdirectories
    public function children(): HasMany
    {
        return $this->hasMany(Directory::class, 'parent_id');
    }

    // Directory has many files
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'directory_id');
    }
}

