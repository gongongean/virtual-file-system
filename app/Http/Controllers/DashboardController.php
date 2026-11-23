<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get virtual disks owned by the logged-in user
        $disks = $user->virtualDisks()->get();

        // Basic statistics
        $diskCount = $disks->count();
        $fileCount = $user->files()->count();
        $folderCount = $user->directories()->count();

        // Total disk capacity
        $totalBlocks = (int) $disks->sum('total_blocks');

        $totalCapacityBytes = (int) $disks->sum(function ($disk) {
            return $disk->total_blocks * $disk->block_size_bytes;
        });

        // Block and storage statistics
        $blockStats = DB::table('disk_blocks')
            ->join('virtual_disks', 'disk_blocks.virtual_disk_id', '=', 'virtual_disks.id')
            ->leftJoin('bitmap_entries', 'disk_blocks.id', '=', 'bitmap_entries.disk_block_id')
            ->where('virtual_disks.owner_id', $user->id)
            ->selectRaw('COUNT(disk_blocks.id) as total_blocks')
            ->selectRaw('SUM(CASE WHEN bitmap_entries.bit_value = 1 THEN 1 ELSE 0 END) as used_blocks')
            ->selectRaw('SUM(CASE WHEN bitmap_entries.bit_value = 0 THEN 1 ELSE 0 END) as free_blocks')
            ->selectRaw('SUM(CASE WHEN bitmap_entries.bit_value = 1 THEN virtual_disks.block_size_bytes ELSE 0 END) as used_storage_bytes')
            ->selectRaw('SUM(CASE WHEN bitmap_entries.bit_value = 0 THEN virtual_disks.block_size_bytes ELSE 0 END) as free_storage_bytes')
            ->first();

        $usedBlocks = (int) ($blockStats->used_blocks ?? 0);
        $freeBlocks = (int) ($blockStats->free_blocks ?? 0);

        $usedStorageBytes = (int) ($blockStats->used_storage_bytes ?? 0);
        $freeStorageBytes = (int) ($blockStats->free_storage_bytes ?? 0);

        $storagePercentage = $totalCapacityBytes > 0
            ? round(($usedStorageBytes / $totalCapacityBytes) * 100, 2)
            : 0;

        // File statistics
        $totalFileSizeBytes = (int) $user->files()->sum('file_size_bytes');

        $allocatedFileBlocks = DB::table('file_blocks')
            ->join('files', 'file_blocks.file_id', '=', 'files.id')
            ->where('files.owner_id', $user->id)
            ->count();

        // Directory statistics
        $rootDirectoryCount = $user->directories()
            ->whereNull('parent_id')
            ->count();

        $subdirectoryCount = $user->directories()
            ->whereNotNull('parent_id')
            ->count();

        // Allocation method statistics
        $allocationCounts = $user->files()
            ->select('allocation_method')
            ->selectRaw('COUNT(*) as total_files')
            ->groupBy('allocation_method')
            ->pluck('total_files', 'allocation_method');

        $allocationStats = [
            'contiguous' => (int) ($allocationCounts['contiguous'] ?? 0),
            'linked' => (int) ($allocationCounts['linked'] ?? 0),
        ];

        return view('dashboard', compact(
            'user',
            'disks',
            'diskCount',
            'fileCount',
            'folderCount',
            'totalBlocks',
            'totalCapacityBytes',
            'usedBlocks',
            'freeBlocks',
            'usedStorageBytes',
            'freeStorageBytes',
            'storagePercentage',
            'totalFileSizeBytes',
            'allocatedFileBlocks',
            'rootDirectoryCount',
            'subdirectoryCount',
            'allocationStats'
        ));
    }
}
