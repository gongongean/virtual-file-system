<?php

namespace App\Services;

use App\Models\VirtualDisk;
use App\Models\DiskBlock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BlockAllocationService
{
    /**
     * Allocate consecutive free blocks using contiguous allocation.
     */
    public function allocateContiguousBlocks(
        VirtualDisk $disk,
        int $numberOfBlocks
    ): Collection {

        if ($numberOfBlocks < 1) {
            return collect();
        }

        return DB::transaction(function () use ($disk, $numberOfBlocks) {

            // Lock free blocks while searching.
            $freeBlocks = DiskBlock::where('virtual_disk_id', $disk->id)
                ->whereHas('bitmapEntry', function ($query) {
                    $query->where('bit_value', false);
                })
                ->orderBy('block_number')
                ->lockForUpdate()
                ->get();

            // Search for a consecutive sequence of free blocks.
            $sequence = collect();

            foreach ($freeBlocks as $block) {

                if (
                    $sequence->isEmpty() ||
                    $block->block_number ===
                        $sequence->last()->block_number + 1
                ) {
                    $sequence->push($block);
                } else {
                    // A gap exists. Start a new sequence.
                    $sequence = collect([$block]);
                }

                // Found enough consecutive blocks.
                if ($sequence->count() === $numberOfBlocks) {
                    break;
                }
            }

            // Check whether a suitable consecutive sequence exists.
            if ($sequence->count() < $numberOfBlocks) {
                throw new RuntimeException(
                    'Not enough consecutive free disk blocks available.'
                );
            }

            // Mark the selected blocks as occupied.
            foreach ($sequence as $block) {
                $block->bitmapEntry()->update([
                    'bit_value' => true,
                ]);
            }

            return $sequence;
        });
    }

    /**
     * Allocate free blocks using linked allocation.
     */
    public function allocateLinkedBlocks(
        VirtualDisk $disk,
        int $numberOfBlocks
    ): Collection {

        if ($numberOfBlocks < 1) {
            return collect();
        }

        return DB::transaction(function () use ($disk, $numberOfBlocks) {

            // Retrieve all available free blocks.
            $freeBlocks = DiskBlock::where('virtual_disk_id', $disk->id)
                ->whereHas('bitmapEntry', function ($query) {
                    $query->where('bit_value', false);
                })
                ->orderBy('block_number')
                ->lockForUpdate()
                ->get();

            // Check whether enough free blocks are available.
            if ($freeBlocks->count() < $numberOfBlocks) {
                throw new RuntimeException(
                    'Not enough free disk blocks available.'
                );
            }

            // Select the required number of blocks.
            $selectedBlocks = $freeBlocks
                ->take($numberOfBlocks)
                ->values();

            // Mark blocks as occupied and link them together.
            foreach ($selectedBlocks as $index => $block) {

                $block->bitmapEntry()->update([
                    'bit_value' => true,
                ]);

                $nextBlock = $selectedBlocks->get($index + 1);

                $block->update([
                    'next_block_id' => $nextBlock?->id,
                ]);
            }

            return $selectedBlocks;
        });
    }

    /**
     * Release blocks back to the virtual disk.
     */
    public function releaseBlocks(Collection $blocks): void
    {
        DB::transaction(function () use ($blocks) {

            foreach ($blocks as $block) {

                $block->bitmapEntry()->update([
                    'bit_value' => false,
                ]);

                $block->update([
                    'block_data' => null,
                    'next_block_id' => null,
                ]);
            }
        });
    }
}
