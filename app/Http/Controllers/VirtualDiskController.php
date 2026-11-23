<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\VirtualDisk;
use App\Models\DiskBlock;
use App\Models\BitmapEntry;
use App\Models\File;

class VirtualDiskController extends Controller
{
    // Display all virtual disks owned by the logged-in user
    public function index()
    {
        $disks = Auth::user()->virtualDisks;

        return view('virtual_disks.index', compact('disks'));
    }

    // Display create virtual disk form
    public function create()
    {
        return view('virtual_disks.create');
    }

    // Create a virtual disk and initialize its storage blocks
    public function store(Request $request)
    {
        $validated = $request->validate([
            'disk_name' => 'required|string|max:255',
            'total_blocks' => 'required|integer|min:1|max:100000',
            'block_size_bytes' => 'required|integer|in:512,1024,2048,4096',
        ]);

        DB::transaction(function () use ($validated) {

            // Create virtual disk
            $disk = Auth::user()->virtualDisks()->create($validated);

            // Initialize disk blocks in batches
            $totalBlocks = (int) $disk->total_blocks;
            $batchSize = 1000;
            $timestamp = now();

            for ($start = 0; $start < $totalBlocks; $start += $batchSize) {

                $end = min($start + $batchSize, $totalBlocks);

                $blockRecords = [];

                for ($i = $start; $i < $end; $i++) {
                    $blockRecords[] = [
                        'virtual_disk_id' => $disk->id,
                        'block_number' => $i,
                        'block_data' => null,
                        'next_block_id' => null,
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                DiskBlock::insert($blockRecords);
            }

            // Create one free bitmap entry for every disk block
            DiskBlock::where('virtual_disk_id', $disk->id)
                ->orderBy('id')
                ->chunk(1000, function ($blocks) use ($timestamp) {

                    $bitmapRecords = [];

                    foreach ($blocks as $block) {
                        $bitmapRecords[] = [
                            'disk_block_id' => $block->id,
                            'bit_value' => false,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }

                    BitmapEntry::insert($bitmapRecords);
                });
        });

        return redirect()
            ->route('virtual-disks.index')
            ->with('success', 'Virtual disk created and initialized successfully!');
    }

    // Display a specific virtual disk and its storage information
    public function show($id)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($id);

        $totalBlocks = $disk->total_blocks;

        // Count occupied blocks using the bitmap
        $usedBlocks = DiskBlock::where('virtual_disk_id', $disk->id)
            ->whereHas('bitmapEntry', function ($query) {
                $query->where('bit_value', true);
            })
            ->count();

        $freeBlocks = $totalBlocks - $usedBlocks;

        // Retrieve bitmap entries for visualization
        $bitmapEntries = DiskBlock::where('virtual_disk_id', $disk->id)
            ->with('bitmapEntry')
            ->orderBy('block_number')
            ->get();

        // Calculate storage capacity
        $blockSize = $disk->block_size_bytes;

        $totalCapacity = $totalBlocks * $blockSize;
        $usedCapacity = $usedBlocks * $blockSize;
        $freeCapacity = $freeBlocks * $blockSize;

        // Count directories and files
        $directories = $disk->directories()->count();

        $files = File::where('virtual_disk_id', $disk->id)->count();

        return view('virtual_disks.show', compact(
            'disk',
            'totalBlocks',
            'usedBlocks',
            'freeBlocks',
            'bitmapEntries',
            'totalCapacity',
            'usedCapacity',
            'freeCapacity',
            'directories',
            'files'
        ));
    }

    // Display edit virtual disk form
    public function edit($id)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($id);

        return view('virtual_disks.edit', compact('disk'));
    }

    // Update an existing virtual disk and safely expand its capacity
    public function update(Request $request, $id)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($id);

        $validated = $request->validate([
            'disk_name' => 'required|string|max:255',
            'total_blocks' => 'required|integer|min:1|max:100000',
        ]);

        DB::transaction(function () use ($disk, $validated) {

            // Lock the disk during expansion
            $disk = VirtualDisk::where('id', $disk->id)
                ->lockForUpdate()
                ->firstOrFail();

            $currentBlocks = (int) $disk->total_blocks;
            $newTotalBlocks = (int) $validated['total_blocks'];

            // Prevent disk shrinking
            if ($newTotalBlocks < $currentBlocks) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'total_blocks' => 'Disk capacity cannot be reduced.',
                ]);
            }

            // Verify existing disk blocks
            $existingBlockCount = DiskBlock::where(
                'virtual_disk_id',
                $disk->id
            )->count();

            // Verify existing bitmap entries
            $existingBitmapCount = BitmapEntry::whereHas(
                'diskBlock',
                function ($query) use ($disk) {
                    $query->where('virtual_disk_id', $disk->id);
                }
            )->count();

            if (
                $existingBlockCount !== $currentBlocks ||
                $existingBitmapCount !== $currentBlocks
            ) {
                throw new \RuntimeException(
                    'Disk block or bitmap records are inconsistent. Expansion cancelled.'
                );
            }

            // Calculate additional blocks
            $additionalBlocks = $newTotalBlocks - $currentBlocks;

            if ($additionalBlocks > 0) {

                $timestamp = now();
                $batchSize = 1000;

                // Determine the next block number
                $startBlockNumber = (int) (
                    DiskBlock::where('virtual_disk_id', $disk->id)
                        ->max('block_number') ?? -1
                ) + 1;

                // Create additional blocks in batches
                for (
                    $offset = 0;
                    $offset < $additionalBlocks;
                    $offset += $batchSize
                ) {

                    $batchCount = min(
                        $batchSize,
                        $additionalBlocks - $offset
                    );

                    $blockRecords = [];

                    for ($i = 0; $i < $batchCount; $i++) {

                        $blockRecords[] = [
                            'virtual_disk_id' => $disk->id,
                            'block_number' => $startBlockNumber + $offset + $i,
                            'block_data' => null,
                            'next_block_id' => null,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }

                    // Insert new disk blocks
                    DiskBlock::insert($blockRecords);

                    // Retrieve newly inserted blocks
                    $newBlocks = DiskBlock::where(
                        'virtual_disk_id',
                        $disk->id
                    )
                        ->whereBetween('block_number', [
                            $startBlockNumber + $offset,
                            $startBlockNumber + $offset + $batchCount - 1,
                        ])
                        ->orderBy('block_number')
                        ->get(['id']);

                    // Initialize bitmap entries as free
                    $bitmapRecords = [];

                    foreach ($newBlocks as $block) {

                        $bitmapRecords[] = [
                            'disk_block_id' => $block->id,
                            'bit_value' => false,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ];
                    }

                    BitmapEntry::insert($bitmapRecords);
                }
            }

            // Update disk name and capacity
            $disk->update([
                'disk_name' => $validated['disk_name'],
                'total_blocks' => $newTotalBlocks,
            ]);
        });

        return redirect()
            ->route('virtual-disks.index')
            ->with(
                'success',
                'Virtual disk updated and capacity expanded successfully!'
            );
    }

    // Delete a virtual disk
    public function destroy($id)
    {
        $disk = Auth::user()
            ->virtualDisks()
            ->findOrFail($id);

        $disk->delete();

        return redirect()
            ->route('virtual-disks.index')
            ->with('success', 'Virtual disk deleted successfully!');
    }
}
