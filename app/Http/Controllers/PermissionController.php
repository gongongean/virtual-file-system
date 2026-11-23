<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Permission;
use App\Models\User;
use App\Models\VirtualDisk;
use App\Models\Directory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Display the sharing form.
     */
    public function create($virtual_disk, $directory, $file)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $folder = Directory::where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($directory);

        $fileRecord = File::where('virtual_disk_id', $disk->id)
            ->where('directory_id', $folder->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        $permissions = $fileRecord->permissions()
            ->with('user')
            ->get();

        $users = User::where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get();

        return view('permissions.create', compact(
            'disk',
            'folder',
            'fileRecord',
            'permissions',
            'users'
        ));
    }

    /**
     * Share a file with another registered user.
     */
    public function store(
        Request $request,
        $virtual_disk,
        $directory,
        $file
    ) {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $folder = Directory::where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($directory);

        $fileRecord = File::where('virtual_disk_id', $disk->id)
            ->where('directory_id', $folder->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        $validated = $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'can_read' => 'nullable|boolean',
            'can_write' => 'nullable|boolean',
            'can_execute' => 'nullable|boolean',
        ]);

        $recipient = User::where(
            'email',
            $validated['recipient_email']
        )->firstOrFail();

        if ($recipient->id === Auth::id()) {
            return back()->withErrors([
                'recipient_email' => 'You cannot share a file with yourself.',
            ])->withInput();
        }

        $canRead = $request->boolean('can_read');
        $canWrite = $request->boolean('can_write');
        $canExecute = $request->boolean('can_execute');

        if (!$canRead && !$canWrite && !$canExecute) {
            return back()->withErrors([
                'permissions' => 'Select at least one permission.',
            ])->withInput();
        }

        Permission::updateOrCreate(
            [
                'file_id' => $fileRecord->id,
                'user_id' => $recipient->id,
            ],
            [
                'can_read' => $canRead,
                'can_write' => $canWrite,
                'can_execute' => $canExecute,
            ]
        );

        return redirect()
            ->route('permissions.create', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id,
            ])
            ->with('success', 'File permissions saved successfully.');
    }

    /**
     * Display files shared with the authenticated user.
     */
    public function sharedWithMe()
    {
        $permissions = Auth::user()
            ->permissions()
            ->with([
                'file.owner',
                'file.virtualDisk',
                'file.directory',
            ])
            ->where(function ($query) {
                $query->where('can_read', true)
                    ->orWhere('can_write', true);
            })
            ->get();

        return view('permissions.shared', compact('permissions'));
    }

    /**
     * Revoke a user's permission to access a file.
     */
    public function destroy(
        $virtual_disk,
        $directory,
        $file,
        $permission
    ) {
        // Verify that the virtual disk belongs to the logged-in user.
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        // Verify directory ownership.
        $folder = Directory::where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($directory);

        // Verify file ownership and directory relationship.
        $fileRecord = File::where('virtual_disk_id', $disk->id)
            ->where('directory_id', $folder->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        // Only delete a permission belonging to this particular file.
        $permissionRecord = Permission::where('file_id', $fileRecord->id)
            ->findOrFail($permission);

        // Permanently revoke the selected user's permission.
        $permissionRecord->delete();

        return redirect()
            ->route('permissions.create', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id,
            ])
            ->with('success', 'User permission revoked successfully.');
    }
}
