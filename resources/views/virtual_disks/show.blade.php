
@extends('layout.app')

@section('content')

<div class="container py-4 virtual-disk-info-page">

    {{-- Page Header --}}
    <div class="mb-4">
        <h2 class="page-title">{{ $disk->disk_name }}</h2>
        <p class="page-subtitle mb-0">Virtual Disk Information</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Block Information Cards --}}
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h6>Total Blocks</h6>
                    <h3>{{ number_format($totalBlocks) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h6>Occupied Blocks</h6>
                    <h3>{{ number_format($usedBlocks) }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card info-card h-100">
                <div class="card-body">
                    <h6>Free Blocks</h6>
                    <h3>{{ number_format($freeBlocks) }}</h3>
                </div>
            </div>
        </div>

    </div>

    {{-- Storage Capacity --}}
    <div class="card info-section p-3 mb-4">

        <h5>Storage Capacity</h5>

        <div class="capacity-details">
            <p>
                <span>Total:</span>
                {{ number_format($totalCapacity / 1024, 2) }} KB
            </p>

            <p>
                <span>Used:</span>
                {{ number_format($usedCapacity / 1024, 2) }} KB
            </p>

            <p>
                <span>Free:</span>
                {{ number_format($freeCapacity / 1024, 2) }} KB
            </p>
        </div>

        <div class="progress storage-progress">

            <div class="progress-bar storage-progress-bar"
                 role="progressbar"
                 style="width: {{ $totalBlocks > 0 ? ($usedBlocks / $totalBlocks) * 100 : 0 }}%"
                 aria-valuenow="{{ $totalBlocks > 0 ? ($usedBlocks / $totalBlocks) * 100 : 0 }}"
                 aria-valuemin="0"
                 aria-valuemax="100">

                {{ $totalBlocks > 0 ? round(($usedBlocks / $totalBlocks) * 100, 2) : 0 }}%

            </div>

        </div>

    </div>

    {{-- Bitmap Visualization --}}
    <div class="card info-section p-3 mb-4">

        <h5>Disk Bitmap Visualization</h5>

        <p class="section-description">
            Each square represents one disk block.
        </p>

        <div class="d-flex gap-4 mb-3 flex-wrap">

            <div class="legend-item">
                <span class="bitmap-legend occupied-block"></span>
                Occupied
            </div>

            <div class="legend-item">
                <span class="bitmap-legend free-block"></span>
                Free
            </div>

        </div>

        <div class="bitmap-grid">

            @foreach($bitmapEntries as $entry)

                @php
                    $isOccupied = (bool) ($entry->bitmapEntry?->bit_value ?? false);
                @endphp

                <div
                    class="bitmap-block {{ $isOccupied ? 'occupied-block' : 'free-block' }}"
                    title="Block {{ $entry->block_number }} - {{ $isOccupied ? 'Occupied' : 'Free' }}">
                </div>

            @endforeach

        </div>

    </div>

    {{-- File System Information --}}
    <div class="card info-section p-3 mb-4">

        <h5>File System Information</h5>

        <div class="filesystem-details">

            <p>
                <span>Directories:</span>
                {{ $directories }}
            </p>

            <p>
                <span>Files:</span>
                {{ $files }}
            </p>

            <p>
                <span>Block Size:</span>
                {{ number_format($disk->block_size_bytes) }} bytes
            </p>

        </div>

    </div>

    {{-- Navigation Buttons --}}
    <div class="d-flex gap-2 flex-wrap">

        <a href="{{ route('virtual-disks.edit', $disk->id) }}"
           class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i>
            Edit Disk
        </a>

        <a href="{{ route('directories.index', $disk->id) }}"
           class="btn btn-primary">
            <i class="bi bi-folder2-open me-1"></i>
            Open Disk
        </a>

        <a href="{{ route('virtual-disks.index') }}"
           class="btn btn-secondary">
            
            Back to Disks
        </a>

    </div>

</div>

<style>

    /* Page */

    .virtual-disk-info-page {
        animation: pageFadeIn 0.35s ease;
        color: #e5edf9;
    }

    .page-title {
        font-weight: 700;
        color: #eaf2ff;
    }

    .page-subtitle {
        color: #94a9c4;
    }

    /* Information Cards */

    .info-card,
    .info-section {
        background: #111e32;
        border: 1px solid #243852;
        border-radius: 15px;
        color: #e5edf9;
        transition: border-color 0.2s ease, transform 0.2s ease;
    }

    .info-card:hover {
        border-color: #3b82f6;
        transform: translateY(-2px);
    }

    .info-card h6 {
        color: #9db2cc;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .info-card h3 {
        color: #60a5fa;
        font-weight: 700;
        margin-bottom: 0;
    }

    .info-section h5 {
        color: #eaf2ff;
        font-weight: 700;
        margin-bottom: 18px;
    }

    /* Storage Capacity */

    .capacity-details p,
    .filesystem-details p {
        color: #cbd5e1;
        margin-bottom: 10px;
    }

    .capacity-details span,
    .filesystem-details span {
        color: #8fa9c8;
        font-weight: 600;
    }

    .storage-progress {
        height: 22px;
        background: #29364a;
        border-radius: 8px;
        overflow: hidden;
    }

    .storage-progress-bar {
        background: #246B70;
        font-weight: 600;
        color: white;
        transition: width 0.5s ease;
    }

    /* Bitmap */

    .section-description {
        color: #94a9c4;
        font-size: 14px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #cbd5e1;
        font-size: 13px;
    }

    .bitmap-legend {
        display: inline-block;
        width: 15px;
        height: 15px;
        border-radius: 4px;
    }

    .bitmap-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(22px, 1fr));
        gap: 5px;
    }

    .bitmap-block {
        height: 22px;
        border-radius: 4px;
        transition: filter 0.2s ease;
    }

    .bitmap-block:hover {
        filter: brightness(1.3);
    }

    .occupied-block {
        background: #246B70;
    }

    .free-block {
        background: #35445b;
    }

    /* Standard Button Theme */

    .virtual-disk-info-page .btn-primary {
        background-color: #2583ff !important;
        border-color: #2583ff !important;
        color: #ffffff !important;
    }

    .virtual-disk-info-page .btn-primary:hover {
        background-color: #0869e8 !important;
        border-color: #0869e8 !important;
        color: #ffffff !important;
    }

    .virtual-disk-info-page .btn-secondary {
        background-color: #192942 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    .virtual-disk-info-page .btn-secondary:hover {
        background-color: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    .virtual-disk-info-page .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .virtual-disk-info-page .btn-danger:hover {
        background-color: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
    }

    .virtual-disk-info-page .btn {
        border-radius: 9px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .virtual-disk-info-page .btn:hover {
        transform: translateY(-2px);
    }

    /* Animations */

    @keyframes pageFadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 480px) {
        .bitmap-grid {
            grid-template-columns: repeat(auto-fill, minmax(18px, 1fr));
            gap: 4px;
        }

        .bitmap-block {
            height: 18px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .virtual-disk-info-page,
        .info-card,
        .bitmap-block,
        .virtual-disk-info-page .btn {
            animation: none;
            transition: none;
        }
    }

</style>

@endsection
