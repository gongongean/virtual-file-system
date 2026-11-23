@extends('layout.app')

@section('content')

<style>
/* =========================================
   VIRTUAL FILE SYSTEM - DASHBOARD THEME
========================================= */

.vfs-dashboard {
    --vfs-bg: #080d19;
    --vfs-panel: #111a2b;
    --vfs-panel-light: #17243a;
    --vfs-blue: #2563eb;
    --vfs-blue-light: #3b82f6;
    --vfs-text: #e5edff;
    --vfs-muted: #94a3b8;
    --vfs-border: rgba(96, 165, 250, 0.15);

    color: var(--vfs-text);
    min-height: 100vh;
    padding: 30px 15px;
    background:
        radial-gradient(ellipse at top left, rgba(37, 99, 235, .13), transparent 40%),
        radial-gradient(ellipse at bottom right, rgba(29, 78, 216, .09), transparent 40%),
        var(--vfs-bg);
    border-radius: 12px;
    animation: vfsFadeIn .7s ease both;
}

@keyframes vfsFadeIn {
    from {
        opacity: 0;
        transform: translateY(12px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes vfsSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes vfsGlow {
    0%, 100% {
        box-shadow: 0 0 8px rgba(37, 99, 235, .08);
    }
    50% {
        box-shadow: 0 0 18px rgba(37, 99, 235, .22);
    }
}

/* Header */

.vfs-header {
    padding: 28px;
    border: 1px solid var(--vfs-border);
    border-radius: 18px;
    background: linear-gradient(120deg, #142747, #101a2e 65%, #111827);
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
    animation: vfsSlideUp .6s ease both;
}

.vfs-header::after {
    content: "";
    position: absolute;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(37, 99, 235, .12);
    right: -60px;
    top: -100px;
    filter: blur(5px);
}

.vfs-header h1 {
    color: #ffffff;
    font-size: clamp(24px, 4vw, 32px);
    font-weight: 750;
    letter-spacing: -.8px;
}

.vfs-header p {
    color: #a8bddc !important;
}

.vfs-header-label {
    color: #60a5fa;
    text-transform: uppercase;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    margin-bottom: 10px;
}

/* Section titles */

.vfs-section-title {
    color: #e5edff;
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 17px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.vfs-section-title::before {
    content: "";
    display: inline-block;
    width: 4px;
    height: 21px;
    border-radius: 5px;
    background: linear-gradient(#60a5fa, #2563eb);
    box-shadow: 0 0 10px rgba(59, 130, 246, .5);
}

/* Dashboard cards */

.vfs-dashboard .card {
    background: linear-gradient(145deg, #142137, #101827);
    color: var(--vfs-text);
    border: 1px solid var(--vfs-border) !important;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, .18) !important;
    transition:
        transform .3s ease,
        border-color .3s ease,
        box-shadow .3s ease,
        background .3s ease;
    overflow: hidden;
    animation: vfsSlideUp .65s ease both;
}

.vfs-dashboard .card:hover {
    transform: translateY(-5px);
    border-color: rgba(59, 130, 246, .55) !important;
    box-shadow: 0 12px 30px rgba(0, 0, 0, .3),
                0 0 15px rgba(37, 99, 235, .12) !important;
}

.vfs-dashboard .card-body {
    padding: 23px;
}

.vfs-dashboard .text-muted {
    color: #94a3b8 !important;
}

.vfs-dashboard h2,
.vfs-dashboard h3 {
    color: #f1f5ff;
    font-weight: 750;
    letter-spacing: -.5px;
}

.vfs-dashboard h6 {
    font-size: 12px;
    font-weight: 650;
    letter-spacing: .6px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

/* Statistics cards */

.vfs-stat-card {
    position: relative;
}

.vfs-stat-card::after {
    content: "";
    position: absolute;
    right: -20px;
    bottom: -35px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: rgba(37, 99, 235, .08);
    transition: transform .4s ease;
}

.vfs-stat-card:hover::after {
    transform: scale(1.5);
}

.vfs-stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 43px;
    height: 43px;
    border-radius: 12px;
    background: rgba(37, 99, 235, .16);
    color: #60a5fa;
    border: 1px solid rgba(59, 130, 246, .25);
    font-size: 20px;
    margin-bottom: 17px;
}

/* Storage progress */

.vfs-progress {
    height: 13px;
    background: #26354c;
    border-radius: 20px;
    overflow: hidden;
}

.vfs-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #1d4ed8, #3b82f6, #60a5fa);
    border-radius: 20px;
    box-shadow: 0 0 12px rgba(59, 130, 246, .45);
    transition: width 1.2s ease;
}

/* Tables */

.vfs-dashboard .table {
    --bs-table-bg: transparent;
    --bs-table-color: #dbeafe;
    --bs-table-border-color: rgba(148, 163, 184, .12);
    margin-bottom: 0;
}

.vfs-dashboard .table thead th {
    background: #1b2b44;
    color: #93c5fd;
    border-color: rgba(96, 165, 250, .15);
    padding: 15px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .6px;
    font-weight: 700;
}

.vfs-dashboard .table tbody td {
    padding: 15px;
    border-color: rgba(148, 163, 184, .12);
    vertical-align: middle;
}

.vfs-dashboard .table-hover tbody tr {
    transition: background .25s ease;
}

.vfs-dashboard .table-hover tbody tr:hover {
    background: rgba(37, 99, 235, .10);
    color: #ffffff;
}

/* Charts */

.vfs-chart-container {
    height: 300px;
    position: relative;
}

/* Quick action buttons */

.vfs-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 650;
    font-size: 13px;
    text-decoration: none;
    border: 1px solid transparent;
    transition: all .3s ease;
}

.vfs-btn-primary {
    background: linear-gradient(120deg, #1d4ed8, #3b82f6);
    color: white;
    box-shadow: 0 5px 15px rgba(37, 99, 235, .2);
}

.vfs-btn-primary:hover {
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(37, 99, 235, .4);
}

.vfs-btn-secondary {
    background: #17243a;
    border-color: rgba(96, 165, 250, .25);
    color: #bfdbfe;
}

.vfs-btn-secondary:hover {
    background: #1e3a65;
    border-color: #3b82f6;
    color: white;
    transform: translateY(-3px);
}

/* Small labels */

.vfs-blue-text {
    color: #60a5fa !important;
}

.vfs-green-text {
    color: #34d399 !important;
}

.vfs-red-text {
    color: #fb7185 !important;
}

/* Chart cards */

.vfs-chart-card {
    min-height: 380px;
}

/* Responsive */

@media (max-width: 768px) {
    .vfs-dashboard {
        padding: 18px 10px;
    }

    .vfs-header {
        padding: 22px 18px;
    }

    .vfs-dashboard .card-body {
        padding: 18px;
    }

    .vfs-chart-container {
        height: 260px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .vfs-dashboard *,
    .vfs-dashboard *::before,
    .vfs-dashboard *::after {
        animation-duration: .01ms !important;
        transition-duration: .01ms !important;
    }
}
</style>

<div class="vfs-dashboard">

    <div class="container-fluid" style="max-width: 1250px;">

        {{-- DASHBOARD HEADER --}}
        <div class="vfs-header">
            <div class="vfs-header-label">Virtual File System</div>

            <h1>Dashboard Overview</h1>

            <p class="mb-0">
                Welcome back, {{ $user->name }}!
                Here's your system storage and file activity overview.
            </p>
        </div>


        {{-- SYSTEM OVERVIEW --}}
        <h4 class="vfs-section-title">System Overview</h4>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card vfs-stat-card h-100">
                    <div class="card-body">
                        <div class="vfs-stat-icon">▣</div>
                        <h6 class="text-muted">Virtual Disks</h6>
                        <h2>{{ $diskCount }}</h2>
                        <small class="text-muted">Total virtual disks created</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card vfs-stat-card h-100">
                    <div class="card-body">
                        <div class="vfs-stat-icon">▤</div>
                        <h6 class="text-muted">Files</h6>
                        <h2>{{ $fileCount }}</h2>
                        <small class="text-muted">
                            Total file size:
                            {{ number_format($totalFileSizeBytes / 1024, 2) }} KB
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card vfs-stat-card h-100">
                    <div class="card-body">
                        <div class="vfs-stat-icon">▧</div>
                        <h6 class="text-muted">Folders</h6>
                        <h2>{{ $folderCount }}</h2>
                        <small class="text-muted">
                            Root: {{ $rootDirectoryCount }} |
                            Subfolders: {{ $subdirectoryCount }}
                        </small>
                    </div>
                </div>
            </div>

        </div>


        {{-- STORAGE OVERVIEW --}}
        <h4 class="vfs-section-title">Storage Overview</h4>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="vfs-stat-icon">▣</div>
                        <h6 class="text-muted">Total Capacity</h6>
                        <h3>{{ number_format($totalCapacityBytes / 1048576, 3) }} MB</h3>
                        <small class="text-muted">
                            {{ number_format($totalBlocks) }} total blocks
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="vfs-stat-icon">◩</div>
                        <h6 class="text-muted">Used Storage</h6>
                        <h3 class="vfs-blue-text">
                            {{ number_format($usedStorageBytes / 1048576, 3) }} MB
                        </h3>
                        <small class="text-muted">
                            {{ number_format($usedBlocks) }} occupied blocks
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="vfs-stat-icon">▱</div>
                        <h6 class="text-muted">Available Storage</h6>
                        <h3 class="vfs-green-text">
                            {{ number_format($freeStorageBytes / 1048576, 3) }} MB
                        </h3>
                        <small class="text-muted">
                            {{ number_format($freeBlocks) }} free blocks
                        </small>
                    </div>
                </div>
            </div>

        </div>


        {{-- STORAGE UTILIZATION --}}
        <div class="card mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Storage Utilization</h5>
                        <small class="text-muted">
                            Overall virtual disk capacity usage
                        </small>
                    </div>

                    <h3 class="mb-0 vfs-blue-text">
                        {{ number_format($storagePercentage, 2) }}%
                    </h3>
                </div>

                <div class="vfs-progress mb-3">
                    <div
                        class="vfs-progress-bar"
                        role="progressbar"
                        style="width: {{ min(100, max(0, $storagePercentage)) }}%;"
                        aria-valuenow="{{ $storagePercentage }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-2">
                    <small class="text-muted">
                        Used: {{ number_format($usedBlocks) }} blocks
                    </small>

                    <small class="text-muted">
                        Available: {{ number_format($freeBlocks) }} blocks
                    </small>
                </div>

            </div>
        </div>


        {{-- BLOCK STATISTICS --}}
        <h4 class="vfs-section-title">Block Statistics</h4>

        <div class="row g-3 mb-4">

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Total Blocks</h6>
                        <h3>{{ number_format($totalBlocks) }}</h3>
                        <small class="text-muted">Total disk block capacity</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Occupied Blocks</h6>
                        <h3 class="vfs-red-text">
                            {{ number_format($usedBlocks) }}
                        </h3>
                        <small class="text-muted">Currently allocated blocks</small>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Free Blocks</h6>
                        <h3 class="vfs-green-text">
                            {{ number_format($freeBlocks) }}
                        </h3>
                        <small class="text-muted">Available disk blocks</small>
                    </div>
                </div>
            </div>

        </div>


        {{-- FILE ALLOCATION STATISTICS --}}
        <h4 class="vfs-section-title">File Allocation Statistics</h4>

        <div class="card mb-4">
            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-4">
                        <h6 class="text-muted">Contiguous Allocation</h6>
                        <h3 class="vfs-blue-text">
                            {{ $allocationStats['contiguous'] }}
                        </h3>
                        <small class="text-muted">Files using contiguous allocation</small>
                    </div>

                    <div class="col-md-4">
                        <h6 class="text-muted">Linked Allocation</h6>
                        <h3 class="vfs-blue-text">
                            {{ $allocationStats['linked'] }}
                        </h3>
                        <small class="text-muted">Files using linked allocation</small>
                    </div>

                    <div class="col-md-4">
                        <h6 class="text-muted">Allocated File Blocks</h6>
                        <h3>
                            {{ number_format($allocatedFileBlocks) }}
                        </h3>
                        <small class="text-muted">Total blocks assigned to files</small>
                    </div>

                </div>

            </div>
        </div>


        {{-- VIRTUAL DISK DETAILS --}}
        <h4 class="vfs-section-title">Virtual Disk Details</h4>

        <div class="card mb-4">
            <div class="card-body">

                @if($disks->isNotEmpty())

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">

                            <thead>
                                <tr>
                                    <th>Disk Name</th>
                                    <th>Total Blocks</th>
                                    <th>Block Size</th>
                                    <th>Total Capacity</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($disks as $disk)
                                    <tr>
                                        <td>
                                            <strong>{{ $disk->disk_name }}</strong>
                                        </td>

                                        <td>
                                            {{ number_format($disk->total_blocks) }}
                                        </td>

                                        <td>
                                            {{ number_format($disk->block_size_bytes) }} bytes
                                        </td>

                                        <td class="vfs-blue-text">
                                            <strong>
                                                {{ number_format(($disk->total_blocks * $disk->block_size_bytes) / 1048576, 3) }}
                                                MB
                                            </strong>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                @else

                    <div class="text-center py-4">
                        <h5 class="text-muted">No Virtual Disks Yet</h5>
                        <p class="text-muted mb-0">
                            You have not created any virtual disks yet.
                        </p>
                    </div>

                @endif

            </div>
        </div>


        {{-- DASHBOARD CHARTS --}}
        <h4 class="vfs-section-title">System Visualization</h4>

        <div class="row g-4 mb-4">

            <div class="col-md-6">
                <div class="card vfs-chart-card h-100">
                    <div class="card-body">

                        <h5 class="mb-4">Storage Distribution</h5>

                        <div class="vfs-chart-container">
                            <canvas id="storageChart"></canvas>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card vfs-chart-card h-100">
                    <div class="card-body">

                        <h5 class="mb-4">File Allocation Methods</h5>

                        <div class="vfs-chart-container">
                            <canvas id="allocationChart"></canvas>
                        </div>

                    </div>
                </div>
            </div>

        </div>


        {{-- QUICK ACTIONS --}}
        <h4 class="vfs-section-title">Quick Actions</h4>

        <div class="d-flex gap-3 flex-wrap mb-4">

            <a href="{{ route('virtual-disks.create') }}"
               class="vfs-btn vfs-btn-primary">
                <span>＋</span>
                Create Virtual Disk
            </a>

            <a href="{{ route('virtual-disks.index') }}"
               class="vfs-btn vfs-btn-secondary">
                <span>▣</span>
                View Virtual Disks
            </a>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const chartTextColor = '#cbd5e1';
    const gridColor = 'rgba(148, 163, 184, 0.10)';

    // STORAGE DISTRIBUTION CHART
    const storageCanvas = document.getElementById('storageChart');

    if (storageCanvas) {

        new Chart(storageCanvas, {
            type: 'doughnut',

            data: {
                labels: ['Occupied Blocks', 'Free Blocks'],

                datasets: [{
                    data: [
                        {{ $usedBlocks }},
                        {{ $freeBlocks }}
                    ],

                    backgroundColor: [
                        '#3b82f6',
                        '#26354c'
                    ],

                    borderColor: '#111a2b',
                    borderWidth: 4,
                    hoverOffset: 10
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                cutout: '70%',

                animation: {
                    animateRotate: true,
                    duration: 1200
                },

                plugins: {
                    legend: {
                        position: 'bottom',

                        labels: {
                            color: chartTextColor,
                            padding: 22,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });

    }


    // FILE ALLOCATION METHODS CHART
    const allocationCanvas = document.getElementById('allocationChart');

    if (allocationCanvas) {

        new Chart(allocationCanvas, {
            type: 'bar',

            data: {
                labels: ['Contiguous', 'Linked'],

                datasets: [{
                    label: 'Number of Files',

                    data: [
                        {{ $allocationStats['contiguous'] }},
                        {{ $allocationStats['linked'] }}
                    ],

                    backgroundColor: [
                        '#3b82f6',
                        '#60a5fa'
                    ],

                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 55
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },

                scales: {

                    x: {
                        ticks: {
                            color: chartTextColor
                        },

                        grid: {
                            display: false
                        },

                        border: {
                            display: false
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0,
                            color: chartTextColor
                        },

                        grid: {
                            color: gridColor
                        },

                        border: {
                            display: false
                        }
                    }
                },

                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

    }

});
</script>

@endpush
