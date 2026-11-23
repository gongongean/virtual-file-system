<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Hidden attributes.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | VIRTUAL DISK RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all virtual disks owned by this user.
     */
    public function virtualDisks(): HasMany
    {
        return $this->hasMany(VirtualDisk::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | DIRECTORY RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all directories owned by this user.
     */
    public function directories(): HasMany
    {
        return $this->hasMany(Directory::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | FILE RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all files owned by this user.
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class, 'owner_id');
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSION RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Get all file permissions assigned to this user.
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'user_id');
    }
}
