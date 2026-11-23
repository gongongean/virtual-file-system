@extends('layout.app')

@section('content')

<div class="container mt-4 shared-files-page">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="page-title">Shared With Me</h2>
            <p class="text-muted mb-0">
                Files that other users have shared with you.
            </p>
        </div>

        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            Back to Dashboard
        </a>
    </div>

    @if($permissions->isEmpty())

        <div class="alert shared-info-alert">
            No files have been shared with you yet.
        </div>

    @else

        <div class="card shared-files-card shadow-sm">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover align-middle shared-files-table">

                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Shared By</th>
                                <th>Virtual Disk</th>
                                <th>Permissions</th>
                                <th>Date Shared</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($permissions as $permission)

                                <tr>
                                    <td>
                                        <strong>
                                            {{ $permission->file->file_name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $permission->file->owner->name ?? 'Unknown' }}
                                    </td>

                                    <td>
                                        {{ $permission->file->virtualDisk->disk_name ?? 'Unknown' }}
                                    </td>

                                    <td>
                                        @if($permission->can_read)
                                            <span class="badge permission-badge badge-read">
                                                Read
                                            </span>
                                        @endif

                                        @if($permission->can_write)
                                            <span class="badge permission-badge badge-write">
                                                Write
                                            </span>
                                        @endif

                                        @if($permission->can_execute)
                                            <span class="badge permission-badge badge-execute">
                                                Execute
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $permission->created_at->format('M d, Y') }}
                                    </td>

                                    <td>
                                        @if($permission->can_read)

                                            <a href="{{ route('shared-files.show', [
                                                'virtual_disk' => $permission->file->virtual_disk_id,
                                                'directory' => $permission->file->directory_id,
                                                'file' => $permission->file->id
                                            ]) }}"
                                               class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i>
                                                View File
                                            </a>

                                        @else

                                            <span class="text-muted">
                                                No viewing access
                                            </span>

                                        @endif
                                    </td>
                                </tr>

                            @endforeach
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

    @endif

</div>

<style>
    /* PAGE */
    .shared-files-page {
        color: #e5edf9;
    }

    .shared-files-page .page-title {
        color: #f1f5ff;
        font-weight: 700;
    }

    /* CARD */
    .shared-files-page .shared-files-card {
        background: #111e32;
        border: 1px solid #243852;
        border-radius: 17px;
        color: #e5edf9;
    }

    /* TABLE */
    .shared-files-page .shared-files-table {
        color: #e5edf9;
        margin-bottom: 0;
        border-color: #243852;
    }

    .shared-files-page .shared-files-table thead th {
        background: #192942;
        color: #8fc0ff;
        border-color: #243852;
        font-weight: 700;
        padding: 15px 12px;
    }

    .shared-files-page .shared-files-table tbody td {
        background: #111e32;
        color: #e5edf9;
        border-color: #243852;
        padding: 14px 12px;
    }

    .shared-files-page .shared-files-table tbody tr:hover td {
        background: #192d48;
        color: #ffffff;
    }

    /* BADGES - ONLY BLUE THEME COLORS */
    .shared-files-page .permission-badge {
        display: inline-block;
        padding: 6px 9px;
        border-radius: 7px;
        font-size: 12px;
        font-weight: 700;
        color: #ffffff !important;
        margin: 2px;
    }

    /* Read - Dark blue */
    .shared-files-page .badge-read {
        background: #192942 !important;
        border: 1px solid #59a7ff;
    }

    /* Write - Primary blue */
    .shared-files-page .badge-write {
        background: #2583ff !important;
        border: 1px solid #2583ff;
    }

    /* Execute - Secondary blue */
    .shared-files-page .badge-execute {
        background: #203653 !important;
        border: 1px solid #59a7ff;
    }

    /* PRIMARY BUTTON */
    .shared-files-page .btn-primary {
        background: #2583ff !important;
        border: 1px solid #2583ff !important;
        color: #ffffff !important;
        border-radius: 9px;
        font-weight: 600;
    }

    .shared-files-page .btn-primary:hover {
        background: #0869e8 !important;
        border-color: #0869e8 !important;
        color: #ffffff !important;
    }

    /* SECONDARY BUTTON */
    .shared-files-page .btn-secondary {
        background: #192942 !important;
        border: 1px solid #59a7ff !important;
        color: #ffffff !important;
        border-radius: 9px;
        font-weight: 600;
    }

    .shared-files-page .btn-secondary:hover {
        background: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    /* INFORMATION ALERT */
    .shared-files-page .shared-info-alert {
        background: #192942;
        border: 1px solid #59a7ff;
        color: #e5edf9;
        border-radius: 10px;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .shared-files-page .shared-files-table {
            min-width: 750px;
        }
    }
</style>

@endsection
