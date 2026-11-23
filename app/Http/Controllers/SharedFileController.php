<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Permission;
use App\Models\VirtualDisk;
use App\Models\Directory;
use App\Services\BlockAllocationService;
use App\Services\FilePreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class SharedFileController extends Controller
{
    protected FilePreviewService $filePreviewService;

    public function __construct(FilePreviewService $filePreviewService)
    {
        $this->filePreviewService = $filePreviewService;
    }

    /**
     * Allow only supported text files to use the text editor.
     */
    private function ensureFileIsEditable(File $fileRecord)
    {
        $editableExtensions = [
            'txt', 'md', 'csv', 'log', 'json',
            'xml', 'html', 'css', 'js', 'php',
        ];

        $extension = strtolower($fileRecord->extension ?? '');

        if (!in_array($extension, $editableExtensions, true)) {
            abort(
                403,
                'This file type cannot be edited using the text editor.'
            );
        }
    }

    /**
     * Display a shared file if the user has Read permission.
     */
    public function show($virtual_disk, $directory, $file)
    {
        $fileRecord = File::where('id', $file)
            ->where('virtual_disk_id', $virtual_disk)
            ->where('directory_id', $directory)
            ->firstOrFail();

        $permission = Permission::where('file_id', $fileRecord->id)
            ->where('user_id', Auth::id())
            ->where('can_read', true)
            ->first();

        if (!$permission) {
            abort(403, 'You do not have permission to view this file.');
        }

        $disk = VirtualDisk::findOrFail($virtual_disk);
        $folder = Directory::findOrFail($directory);

        if (
            !$fileRecord->storage_path ||
            !Storage::disk('local')->exists($fileRecord->storage_path)
        ) {
            abort(404, 'Stored file not found.');
        }

        $preview = $this->filePreviewService->preview($fileRecord);

        return view('files.show', array_merge([
            'disk' => $disk,
            'folder' => $folder,
            'fileRecord' => $fileRecord,
            'isShared' => true,
            'permission' => $permission,
        ], $preview));
    }

    /**
     * Download a shared file if the user has Read permission.
     */
    public function download($virtual_disk, $directory, $file)
    {
        $fileRecord = File::where('id', $file)
            ->where('virtual_disk_id', $virtual_disk)
            ->where('directory_id', $directory)
            ->firstOrFail();

        $permission = Permission::where('file_id', $fileRecord->id)
            ->where('user_id', Auth::id())
            ->where('can_read', true)
            ->first();

        if (!$permission) {
            abort(403, 'You do not have permission to download this file.');
        }

        if (
            !$fileRecord->storage_path ||
            !Storage::disk('local')->exists($fileRecord->storage_path)
        ) {
            abort(404, 'Stored file not found.');
        }

        return Storage::disk('local')->download(
            $fileRecord->storage_path,
            $fileRecord->file_name
        );
    }

    /**
     * Display the shared file editor if the user has Write permission.
     */
    public function edit($virtual_disk, $directory, $file)
    {
        $fileRecord = File::where('id', $file)
            ->where('virtual_disk_id', $virtual_disk)
            ->where('directory_id', $directory)
            ->firstOrFail();

        $permission = Permission::where('file_id', $fileRecord->id)
            ->where('user_id', Auth::id())
            ->where('can_write', true)
            ->first();

        if (!$permission) {
            abort(403, 'You do not have permission to edit this file.');
        }

        $this->ensureFileIsEditable($fileRecord);

        $disk = VirtualDisk::findOrFail($virtual_disk);
        $folder = Directory::findOrFail($directory);

        if (
            !$fileRecord->storage_path ||
            !Storage::disk('local')->exists($fileRecord->storage_path)
        ) {
            abort(404, 'Stored file not found.');
        }

        $content = Storage::disk('local')->get(
            $fileRecord->storage_path
        );

        return view('files.edit', [
            'disk' => $disk,
            'folder' => $folder,
            'fileRecord' => $fileRecord,
            'content' => $content,
            'isShared' => true,
            'permission' => $permission,
        ]);
    }

    /**
     * Update a shared file if the user has Write permission.
     */
    public function update(
        Request $request,
        $virtual_disk,
        $directory,
        $file,
        BlockAllocationService $allocationService
    ) {
        $fileRecord = File::where('id', $file)
            ->where('virtual_disk_id', $virtual_disk)
            ->where('directory_id', $directory)
            ->firstOrFail();

        $permission = Permission::where('file_id', $fileRecord->id)
            ->where('user_id', Auth::id())
            ->where('can_write', true)
            ->first();

        if (!$permission) {
            abort(403, 'You do not have permission to modify this file.');
        }

        $this->ensureFileIsEditable($fileRecord);

        $disk = VirtualDisk::findOrFail($virtual_disk);
        $folder = Directory::findOrFail($directory);

        $validated = $request->validate([
            'content' => 'present|string|max:10485760',
        ]);

        $newContent = $validated['content'];
        $newSize = strlen($newContent);

        $blockSize = (int) $disk->block_size_bytes;

        if ($blockSize < 1) {
            abort(500, 'Invalid virtual disk block size.');
        }

        $newBlocksRequired = $newSize > 0
            ? (int) ceil($newSize / $blockSize)
            : 0;

        if (
            !$fileRecord->storage_path ||
            !Storage::disk('local')->exists($fileRecord->storage_path)
        ) {
            abort(404, 'Stored file not found.');
        }

        $oldPath = $fileRecord->storage_path;
        $oldContent = Storage::disk('local')->get($oldPath);

        try {
            DB::transaction(function () use (
                $fileRecord,
                $disk,
                $newContent,
                $newSize,
                $newBlocksRequired,
                $blockSize,
                $oldPath,
                $allocationService
            ) {
                $file = $fileRecord->newQuery()
                    ->whereKey($fileRecord->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $oldFileBlocks = $file->fileBlocks()
                    ->with('diskBlock.bitmapEntry')
                    ->lockForUpdate()
                    ->get();

                $oldDiskBlocks = $oldFileBlocks
                    ->pluck('diskBlock')
                    ->filter();

                foreach ($oldDiskBlocks as $block) {
                    $block->bitmapEntry()->update([
                        'bit_value' => false,
                    ]);

                    $block->update([
                        'block_data' => null,
                        'next_block_id' => null,
                    ]);
                }

                $file->fileBlocks()->delete();

                if ($file->allocation_method === 'contiguous') {
                    $allocatedBlocks =
                        $allocationService->allocateContiguousBlocks(
                            $disk,
                            $newBlocksRequired
                        );

                } elseif ($file->allocation_method === 'linked') {
                    $allocatedBlocks =
                        $allocationService->allocateLinkedBlocks(
                            $disk,
                            $newBlocksRequired
                        );

                } else {
                    throw new RuntimeException(
                        'Unsupported file allocation method.'
                    );
                }

                foreach ($allocatedBlocks as $order => $block) {
                    $blockContent = substr(
                        $newContent,
                        $order * $blockSize,
                        $blockSize
                    );

                    $block->update([
                        'block_data' => $blockContent,
                    ]);

                    if ($file->allocation_method === 'contiguous') {
                        $block->update([
                            'next_block_id' => null,
                        ]);
                    }

                    $file->fileBlocks()->create([
                        'disk_block_id' => $block->id,
                        'block_order' => $order,
                        'is_index_block' => false,
                    ]);
                }

                if (!Storage::disk('local')->put(
                    $oldPath,
                    $newContent
                )) {
                    throw new RuntimeException(
                        'Unable to save the updated file content.'
                    );
                }

                $file->update([
                    'file_size_bytes' => $newSize,
                ]);
            });

        } catch (Throwable $exception) {
            Storage::disk('local')->put(
                $oldPath,
                $oldContent
            );

            if ($exception instanceof RuntimeException) {
                return back()->withInput()->withErrors([
                    'content' => $exception->getMessage(),
                ]);
            }

            throw $exception;
        }

        $totalBlocksAllocated = $newBlocksRequired;

        return redirect()
            ->route('shared-files.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id,
            ])
            ->with(
                'success',
                "Shared file updated successfully! {$totalBlocksAllocated} disk block(s) allocated."
            );
    }
}
