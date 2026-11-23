 @extends('layout.app')

@section('content')

<div class="container py-4 virtual-disks-page">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h2 class="mb-0 page-title">Virtual Disk Management</h2>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                Back to Dashboard
            </a>

            <a href="{{ route('virtual-disks.create') }}" class="btn btn-primary">
                Create Virtual Disk
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @forelse($disks as $disk)

        <div class="card disk-card mb-3">
            <div class="card-body">

                <h5>{{ $disk->disk_name }}</h5>

                <p class="mb-1">
                    Total Blocks: {{ number_format($disk->total_blocks) }}
                </p>

                <p class="mb-3">
                    Block Size: {{ number_format($disk->block_size_bytes) }} bytes
                </p>

                <a href="{{ route('virtual-disks.show', $disk->id) }}"
                   class="btn btn-primary btn-sm">
                    Open Disk
                </a>

                <a href="{{ route('virtual-disks.edit', $disk->id) }}"
                   class="btn btn-primary btn-sm">
                    Edit
                </a>

                <button type="button"
                        class="btn btn-danger btn-sm"
                        onclick="openDiskDeleteModal({{ $disk->id }})">
                    Delete
                </button>

            </div>
        </div>

        {{-- Delete Confirmation Modal --}}
        <div id="diskDeleteModal{{ $disk->id }}"
             class="delete-modal">

            <div class="delete-modal-content">

                <div class="delete-icon">
                    <i class="bi bi-exclamation-lg"></i>
                </div>

                <h4>Confirm Deletion</h4>

                <p class="delete-description">
                    Are you sure you want to delete this virtual disk?
                </p>

                <div class="disk-name-box">
                    <i class="bi bi-hdd-stack me-2"></i>
                    {{ $disk->disk_name }}
                </div>

                <p class="delete-warning">
                    <i class="bi bi-info-circle me-1"></i>
                    This action cannot be undone. All contents of this virtual disk will also be deleted.
                </p>

                <div class="delete-actions">

                    <button type="button"
                            class="btn cancel-button"
                            onclick="closeDiskDeleteModal({{ $disk->id }})">
                        Cancel
                    </button>

                    <form action="{{ route('virtual-disks.destroy', $disk->id) }}"
                          method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn confirm-delete-button">
                            <i class="bi bi-trash me-1"></i>
                            Yes, Delete
                        </button>
                    </form>

                </div>

            </div>
        </div>

    @empty

        <div class="alert alert-info">
            No virtual disks created yet.
        </div>

    @endforelse

</div>

<style>
    .virtual-disks-page {
        animation: pageFadeIn 0.35s ease;
    }

    .page-title {
        font-weight: 700;
    }

    .disk-card {
        background: #111e32;
        border: 1px solid #243852;
        border-radius: 16px;
        color: #e5edf9;
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .disk-card:hover {
        transform: translateY(-2px);
        border-color: #3b82f6;
    }

    .disk-card h5 {
        font-weight: 700;
        color: #eaf2ff;
    }

    /* STANDARDIZED BUTTON THEME */

    .virtual-disks-page .btn {
        border-radius: 9px !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    /* PRIMARY BLUE */

    .virtual-disks-page .btn-primary,
    .virtual-disks-page .btn-success,
    .virtual-disks-page .btn-info,
    .virtual-disks-page .btn-warning {
        background: #2583ff !important;
        border-color: #2583ff !important;
        color: #ffffff !important;
    }

    .virtual-disks-page .btn-primary:hover,
    .virtual-disks-page .btn-success:hover,
    .virtual-disks-page .btn-info:hover,
    .virtual-disks-page .btn-warning:hover {
        background: #0869e8 !important;
        border-color: #0869e8 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
    }

    /* SECONDARY DARK BLUE */

    .virtual-disks-page .btn-secondary {
        background: #192942 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    .virtual-disks-page .btn-secondary:hover {
        background: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
        transform: translateY(-2px);
    }

    /* DELETE BUTTONS ON PAGE */

    .virtual-disks-page .btn-danger {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .virtual-disks-page .btn-danger:hover {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
    }

    /* DELETE MODAL OVERLAY */

    .delete-modal {
        display: none;
        position: fixed;
        z-index: 99999;
        inset: 0;
        width: 100%;
        height: 100%;
        background: rgba(2, 8, 20, 0.78);
        backdrop-filter: blur(5px);
        justify-content: center;
        align-items: center;
        padding: 15px;
    }

    /* COMPACT MODAL */

    .delete-modal-content {
        background: linear-gradient(145deg, #14243b, #0c1729);
        width: 100%;
        max-width: 370px;
        padding: 23px 25px;
        border: 1px solid #2b4261;
        border-radius: 17px;
        box-shadow: 0 18px 55px rgba(0, 0, 0, 0.45);
        text-align: center;
        color: #e5edf9;
        animation: modalFadeIn 0.22s ease;
    }

    .delete-icon {
        width: 45px;
        height: 45px;
        border: 2px solid #dc3545;
        color: #f87171;
        background: rgba(220, 53, 69, 0.10);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 13px;
        font-size: 25px;
    }

    .delete-modal-content h4 {
        font-size: 19px;
        font-weight: 700;
        margin-bottom: 9px;
        color: #f1f5ff;
    }

    .delete-description {
        color: #a9bad1;
        font-size: 13px;
        margin-bottom: 13px;
    }

    .disk-name-box {
        background: #1b2d47;
        border: 1px solid #304967;
        border-radius: 9px;
        padding: 9px 12px;
        color: #60a5fa;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .delete-warning {
        font-size: 11.5px;
        line-height: 1.5;
        color: #a9bad1;
        margin-bottom: 17px;
    }

    .delete-warning i {
        color: #fbbf24;
    }

    .delete-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
    }

    .delete-actions .btn {
        border-radius: 8px;
        padding: 8px 15px;
        font-size: 12.5px;
        font-weight: 600;
        transition: 0.2s ease;
    }

    /* CANCEL BUTTON */

    .cancel-button {
        background: #192942 !important;
        border: 1px solid #59a7ff !important;
        color: #ffffff !important;
    }

    .cancel-button:hover {
        background: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    /* CONFIRM DELETE BUTTON - INDEPENDENT OF PAGE CONTAINER */

    .confirm-delete-button,
    .confirm-delete-button:focus,
    .confirm-delete-button:active,
    .confirm-delete-button:visited {
        display: inline-block !important;
        background: #dc3545 !important;
        background-color: #dc3545 !important;
        border: 1px solid #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
        opacity: 1 !important;
        visibility: visible !important;
        filter: none !important;
        box-shadow: none !important;
        font-weight: 700 !important;
        text-decoration: none !important;
    }

    .confirm-delete-button i {
        color: #ffffff !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .confirm-delete-button:hover {
        background: #bb2d3b !important;
        background-color: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
        opacity: 1 !important;
        transform: translateY(-1px);
    }

    .confirm-delete-button:hover i {
        color: #ffffff !important;
    }

    .delete-actions form {
        margin: 0;
    }

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.94) translateY(8px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

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
        .delete-modal-content {
            max-width: 340px;
            padding: 20px;
        }

        .delete-actions {
            flex-wrap: wrap;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .virtual-disks-page,
        .delete-modal-content {
            animation: none;
        }

        .disk-card,
        .delete-actions .btn {
            transition: none;
        }
    }
</style>

<script>
    function openDiskDeleteModal(diskId) {
        const modal = document.getElementById('diskDeleteModal' + diskId);

        if (modal) {
            document.body.appendChild(modal);
            modal.style.display = 'flex';
        }
    }

    function closeDiskDeleteModal(diskId) {
        const modal = document.getElementById('diskDeleteModal' + diskId);

        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Close modal when clicking outside the confirmation box
    document.querySelectorAll('.delete-modal').forEach(function(modal) {
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    });

    // Close modal using Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            document.querySelectorAll('.delete-modal').forEach(function(modal) {
                modal.style.display = 'none';
            });
        }
    });
</script>

@endsection