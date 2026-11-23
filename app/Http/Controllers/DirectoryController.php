<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\VirtualDisk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectoryController extends Controller
{
    // Display root directory or contents of a selected folder
    public function index($virtual_disk, Request $request)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $currentDirectory = null;

        // Check if a folder is selected
        if ($request->filled('parent_id')) {
            $currentDirectory = Directory::where('virtual_disk_id', $disk->id)
                ->where('owner_id', Auth::id())
                ->findOrFail($request->parent_id);
        }

        // Get folders inside the current directory
        $directories = $disk->directories()
            ->where('owner_id', Auth::id())
            ->where('parent_id', $currentDirectory?->id)
            ->orderBy('directory_name')
            ->get();

        // Get files inside the current directory
        $files = collect();

        return view('directories.index', compact(
            'disk',
            'currentDirectory',
            'directories',
            'files'
        ));
    }

    // Display create folder form
    public function create(Request $request, $virtual_disk)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $parentId = $request->query('parent_id');

        $parentDirectory = null;

        if ($parentId) {
            $parentDirectory = Directory::where('virtual_disk_id', $disk->id)
                ->where('owner_id', Auth::id())
                ->findOrFail($parentId);
        }

        return view('directories.create', compact(
            'disk',
            'parentDirectory'
        ));
    }

    // Save a new folder
    public function store(Request $request, $virtual_disk)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $validated = $request->validate([
            'directory_name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer',
        ]);

        $parentId = $validated['parent_id'] ?? null;

        // Verify parent directory belongs to this disk
        if ($parentId) {
            Directory::where('virtual_disk_id', $disk->id)
                ->where('owner_id', Auth::id())
                ->findOrFail($parentId);
        }

        // Prevent duplicate folder names in the same location
        $exists = $disk->directories()
            ->where('owner_id', Auth::id())
            ->where('parent_id', $parentId)
            ->where('directory_name', $validated['directory_name'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'directory_name' => 'A folder with this name already exists.'
                ]);
        }

        $disk->directories()->create([
            'owner_id' => Auth::id(),
            'parent_id' => $parentId,
            'directory_name' => $validated['directory_name'],
        ]);

        return redirect()
            ->route('directories.index', [
                'virtual_disk' => $disk->id,
                'parent_id' => $parentId,
            ])
            ->with('success', 'Folder created successfully!');
    }

    // Open a selected folder
    public function show($virtual_disk, $directory)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $currentDirectory = $disk->directories()
            ->where('owner_id', Auth::id())
            ->findOrFail($directory);

        $directories = $currentDirectory->children()
            ->orderBy('directory_name')
            ->get();

        $files = $currentDirectory->files()
            ->orderBy('file_name')
            ->get();

        return view('directories.index', compact(
            'disk',
            'currentDirectory',
            'directories',
            'files'
        ));
    }

    // Delete a folder
    public function destroy($virtual_disk, $directory)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $folder = $disk->directories()
            ->where('owner_id', Auth::id())
            ->findOrFail($directory);

        $parentId = $folder->parent_id;

        $folder->delete();

        return redirect()
            ->route('directories.index', [
                'virtual_disk' => $disk->id,
                'parent_id' => $parentId,
            ])
            ->with('success', 'Folder deleted successfully!');
    }
}

