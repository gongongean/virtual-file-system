<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Services\BlockAllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class FileController extends Controller
{
    /**
     * Verify that the directory belongs to
     * the authenticated user's virtual disk.
     */
    private function getDirectory($virtual_disk, $directory)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($virtual_disk);

        $folder = Directory::where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($directory);

        return [$disk, $folder];
    }

    /**
     * Check whether a file is safe to edit as text.
     */
    private function ensureFileIsEditable($fileRecord)
    {
        $editableExtensions = [
            'txt',
            'md',
            'csv',
            'log',
            'json',
            'xml',
            'html',
            'css',
            'js',
            'php',
        ];

        $extension = strtolower($fileRecord->extension ?? '');

        if (!in_array($extension, $editableExtensions, true)) {
            abort(
                403,
                'This file type cannot be edited using the text editor. Download the file to edit it.'
            );
        }
    }

    /**
     * Display the Create File editor.
     */
    public function create($virtual_disk, $directory)
    {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        return view('files.create', [
            'disk' => $disk,
            'folder' => $folder,
        ]);
    }

    /**
     * Save a newly created text file.
     */
    public function storeCreated(
        Request $request,
        $virtual_disk,
        $directory,
        BlockAllocationService $allocationService
    ) {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $validated = $request->validate([
            'file_name' => 'required|string|max:230',
            'extension' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[A-Za-z0-9]+$/',
            ],
            'content' => 'nullable|string',
            'allocation_method' => 'required|in:contiguous,linked',
        ]);

        $baseName = trim($validated['file_name']);
        $extension = $validated['extension'] ?? null;
        $content = $validated['content'] ?? '';

        if (
            $baseName === '' ||
            $baseName === '.' ||
            $baseName === '..' ||
            str_contains($baseName, '/') ||
            str_contains($baseName, '\\')
        ) {
            return back()->withInput()->withErrors([
                'file_name' => 'Please enter a valid file name.',
            ]);
        }

        $fullName = $baseName;

        if ($extension) {
            $fullName .= '.' . $extension;
        }

        $exists = $folder->files()
            ->where('file_name', $fullName)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'file_name' =>
                    'A file with this name already exists in this folder.',
            ]);
        }

        $fileSize = strlen($content);
        $blockSize = (int) $disk->block_size_bytes;

        if ($blockSize < 1) {
            throw new RuntimeException('Invalid disk block size.');
        }

        $blocksRequired = $fileSize > 0
            ? (int) ceil($fileSize / $blockSize)
            : 0;

        $allocationMethod = $validated['allocation_method'];

        $storedName = Str::uuid() . '.' . ($extension ?: 'file');

        $storagePath = 'virtual-disks/' . $disk->id .
            '/files/' . $storedName;

        $saved = Storage::disk('local')->put(
            $storagePath,
            $content
        );

        if (!$saved) {
            return back()->withInput()->withErrors([
                'content' => 'Unable to save the file contents.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $disk,
                $folder,
                $fullName,
                $extension,
                $fileSize,
                $storagePath,
                $blocksRequired,
                $allocationMethod,
                $allocationService,
                $content,
                $blockSize
            ) {

                $file = $folder->files()->create([
                    'virtual_disk_id' => $disk->id,
                    'owner_id' => Auth::id(),
                    'file_name' => $fullName,
                    'extension' => $extension,
                    'file_size_bytes' => $fileSize,
                    'storage_path' => $storagePath,
                    'allocation_method' => $allocationMethod,
                ]);

                if ($allocationMethod === 'contiguous') {
                    $allocatedBlocks =
                        $allocationService->allocateContiguousBlocks(
                            $disk,
                            $blocksRequired
                        );
                } else {
                    $allocatedBlocks =
                        $allocationService->allocateLinkedBlocks(
                            $disk,
                            $blocksRequired
                        );
                }

                foreach ($allocatedBlocks as $order => $block) {
                    $blockContent = substr(
                        $content,
                        $order * $blockSize,
                        $blockSize
                    );

                    $block->update([
                        'block_data' => $blockContent,
                    ]);

                    if ($allocationMethod === 'contiguous') {
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
            });

        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storagePath);

            if ($exception instanceof RuntimeException) {
                return back()->withInput()->withErrors([
                    'file_name' => $exception->getMessage(),
                ]);
            }

            throw $exception;
        }

        $totalBlocksAllocated = $blocksRequired;

        return redirect()
            ->route('directories.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
            ])
            ->with(
                'success',
                "File created successfully using {$allocationMethod} allocation! {$totalBlocksAllocated} disk block(s) allocated."
            );
    }

    /**
     * Upload a file using the selected allocation method.
     */
    public function store(
        Request $request,
        $virtual_disk,
        $directory,
        BlockAllocationService $allocationService
    ) {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $validated = $request->validate([
            'uploaded_file' => 'required|file|max:10240',
            'allocation_method' => 'nullable|in:contiguous,linked',
        ]);

        $allocationMethod = $validated['allocation_method']
            ?? 'contiguous';

        $uploadedFile = $request->file('uploaded_file');
        $originalName = $uploadedFile->getClientOriginalName();

        $exists = $folder->files()
            ->where('file_name', $originalName)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'uploaded_file' =>
                    'A file with this name already exists in this folder.',
            ]);
        }

        $fileSize = $uploadedFile->getSize();
        $blockSize = (int) $disk->block_size_bytes;

        if ($blockSize < 1) {
            throw new RuntimeException('Invalid disk block size.');
        }

        $blocksRequired = (int) ceil($fileSize / $blockSize);

        $storedName = Str::uuid() . '.' .
            ($uploadedFile->getClientOriginalExtension() ?: 'file');

        $storageDirectory = 'virtual-disks/' . $disk->id . '/files';

        $storagePath = $storageDirectory . '/' . $storedName;

        $storedPath = Storage::disk('local')->putFileAs(
            $storageDirectory,
            $uploadedFile,
            $storedName
        );

        if (!$storedPath) {
            return back()->withErrors([
                'uploaded_file' => 'Unable to save the uploaded file.',
            ]);
        }

        $content = Storage::disk('local')->get($storagePath);

        try {
            DB::transaction(function () use (
                $disk,
                $folder,
                $originalName,
                $uploadedFile,
                $fileSize,
                $storagePath,
                $blocksRequired,
                $allocationMethod,
                $allocationService,
                $content,
                $blockSize
            ) {

                $file = $folder->files()->create([
                    'virtual_disk_id' => $disk->id,
                    'owner_id' => Auth::id(),
                    'file_name' => $originalName,
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'file_size_bytes' => $fileSize,
                    'storage_path' => $storagePath,
                    'allocation_method' => $allocationMethod,
                ]);

                if ($allocationMethod === 'contiguous') {
                    $allocatedBlocks =
                        $allocationService->allocateContiguousBlocks(
                            $disk,
                            $blocksRequired
                        );
                } else {
                    $allocatedBlocks =
                        $allocationService->allocateLinkedBlocks(
                            $disk,
                            $blocksRequired
                        );
                }

                foreach ($allocatedBlocks as $order => $block) {
                    $blockContent = substr(
                        $content,
                        $order * $blockSize,
                        $blockSize
                    );

                    $block->update([
                        'block_data' => $blockContent,
                    ]);

                    if ($allocationMethod === 'contiguous') {
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
            });

        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storagePath);

            if ($exception instanceof RuntimeException) {
                return back()->withInput()->withErrors([
                    'uploaded_file' => $exception->getMessage(),
                ]);
            }

            throw $exception;
        }

        $totalBlocksAllocated = $blocksRequired;

        return redirect()
            ->route('directories.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
            ])
            ->with(
                'success',
                "File uploaded successfully using {$allocationMethod} allocation! {$totalBlocksAllocated} disk block(s) allocated."
            );
    }

    /**
     * Display the Edit File editor.
     */
    public function edit($virtual_disk, $directory, $file)
    {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $fileRecord = $folder->files()
            ->where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        $this->ensureFileIsEditable($fileRecord);

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
        ]);
    }

    /**
     * Save edited content and update disk block allocation.
     */
    public function update(
        Request $request,
        $virtual_disk,
        $directory,
        $file,
        BlockAllocationService $allocationService
    ) {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $fileRecord = $folder->files()
            ->where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        $this->ensureFileIsEditable($fileRecord);

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
            ->route('files.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id,
            ])
            ->with(
                'success',
                "File updated successfully! {$totalBlocksAllocated} disk block(s) allocated."
            );
    }

    /**
     * Display the contents of a file using FilePreviewService.
     */
    public function show($virtual_disk, $directory, $file)
    {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $fileRecord = $folder->files()
            ->where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        if (
            !$fileRecord->storage_path ||
            !Storage::disk('local')->exists($fileRecord->storage_path)
        ) {
            abort(404, 'Stored file not found.');
        }

        $preview = app(\App\Services\FilePreviewService::class)
            ->preview($fileRecord);

        return view('files.show', array_merge([
            'disk' => $disk,
            'folder' => $folder,
            'fileRecord' => $fileRecord,
        ], $preview));
    }

    /**
     * Download a file.
     */
    public function download($virtual_disk, $directory, $file)
    {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $fileRecord = $folder->files()
            ->where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

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
     * Rename a file.
     */
    public function rename(
        Request $request,
        $virtual_disk,
        $directory,
        $file
    ) {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $fileRecord = $folder->files()
            ->where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        $validated = $request->validate([
            'file_name' => 'required|string|max:255',
        ]);

        $newName = trim($validated['file_name']);

        if (
            $newName === '' ||
            $newName === '.' ||
            $newName === '..' ||
            str_contains($newName, '/') ||
            str_contains($newName, '\\')
        ) {
            return back()->withInput()->withErrors([
                'file_name' => 'Please enter a valid file name.',
            ]);
        }

        $originalExtension = $fileRecord->extension
            ?: pathinfo($fileRecord->file_name, PATHINFO_EXTENSION)
            ?: pathinfo($fileRecord->storage_path ?? '', PATHINFO_EXTENSION);

        // Detect the extension entered by the user.
        $enteredExtension = pathinfo(
            $newName,
            PATHINFO_EXTENSION
        );

        // If a new extension was entered, use it.
        // Otherwise, preserve the original extension.
        if ($enteredExtension !== '') {
            $finalName = $newName;
            $finalExtension = $enteredExtension;
        } else {
            $finalName = $originalExtension
                ? $newName . '.' . $originalExtension
                : $newName;

            $finalExtension = $originalExtension ?: null;
        }

        $exists = $folder->files()
            ->where('file_name', $finalName)
            ->where('id', '!=', $fileRecord->id)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors([
                'file_name' => 'A file with this name already exists.',
            ]);
        }

        $fileRecord->update([
            'file_name' => $finalName,
            'extension' => $finalExtension,
        ]);

        return back()->with(
            'success',
            'File renamed successfully!'
        );
    }

    /**
     * Delete a file and release its allocated blocks.
     */
    public function destroy(
        $virtual_disk,
        $directory,
        $file,
        BlockAllocationService $allocationService
    ) {
        [$disk, $folder] = $this->getDirectory(
            $virtual_disk,
            $directory
        );

        $fileRecord = $folder->files()
            ->where('virtual_disk_id', $disk->id)
            ->where('owner_id', Auth::id())
            ->findOrFail($file);

        $storagePath = $fileRecord->storage_path;

        DB::transaction(function () use (
            $fileRecord,
            $allocationService
        ) {

            $fileBlocks = $fileRecord->fileBlocks()
                ->with('diskBlock.bitmapEntry')
                ->get();

            $diskBlocks = $fileBlocks
                ->pluck('diskBlock')
                ->filter();

            $allocationService->releaseBlocks($diskBlocks);

            $fileRecord->delete();
        });

        if ($storagePath) {
            Storage::disk('local')->delete($storagePath);
        }

        return back()->with(
            'success',
            'File deleted successfully! Disk blocks have been released.'
        );
    }
}
