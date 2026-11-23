
@extends('layout.app')

@section('content')

<style>
    /* =====================================
       PERMISSION PAGE THEME
       ===================================== */

    .permission-page {
        color: #e5edf9;
    }

    .permission-page .card {
        background: #111e32;
        border: 1px solid #243852;
        border-radius: 14px;
        color: #e5edf9;
    }

    .permission-page h2,
    .permission-page h5 {
        color: #f1f5ff;
        font-weight: 700;
    }

    .permission-page .text-muted {
        color: #94a9c4 !important;
    }

    /* PRIMARY BLUE BUTTONS */

    .permission-page .btn-primary {
        background: #2583ff !important;
        border-color: #2583ff !important;
        color: #ffffff !important;
    }

    .permission-page .btn-primary:hover {
        background: #0869e8 !important;
        border-color: #0869e8 !important;
        color: #ffffff !important;
    }

    /* SECONDARY DARK BLUE BUTTONS */

    .permission-page .btn-secondary {
        background: #192942 !important;
        border: 1px solid #59a7ff !important;
        color: #ffffff !important;
    }

    .permission-page .btn-secondary:hover {
        background: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    /* DELETE BUTTONS REMAIN RED */

    .permission-page .btn-danger {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .permission-page .btn-danger:hover {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
    }

    .permission-page .btn {
        border-radius: 9px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    /* =====================================
       PERMISSION BADGES
       ===================================== */

    .permission-page .permission-badge {
        display: inline-block;
        background: #2583ff !important;
        color: #ffffff !important;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
        margin-right: 3px;
    }

    /* =====================================
       FORM CONTROLS
       ===================================== */

    .permission-page .form-control,
    .permission-page .form-select {
        background-color: #0c1729;
        border: 1px solid #304967;
        color: #e5edf9;
        border-radius: 8px;
    }

    .permission-page .form-select:focus,
    .permission-page .form-control:focus {
        background-color: #0c1729;
        border-color: #2583ff;
        color: #ffffff;
        box-shadow: 0 0 0 0.2rem rgba(37, 131, 255, 0.18);
    }

    .permission-page .form-select option {
        background: #111e32;
        color: #ffffff;
    }

    .permission-page .form-check-input {
        background-color: #192942;
        border-color: #59a7ff;
    }

    .permission-page .form-check-input:checked {
        background-color: #2583ff;
        border-color: #2583ff;
    }

    .permission-page .form-check-input:focus {
        border-color: #2583ff;
        box-shadow: 0 0 0 0.2rem rgba(37, 131, 255, 0.2);
    }

    .permission-page .form-check-label,
    .permission-page .form-label {
        color: #dbe7f7;
    }

    /* USER ACCESS ITEM */

    .permission-page .access-item {
        background: #14243b;
        border: 1px solid #304967 !important;
        border-radius: 10px;
    }

    /* =====================================
       DELETE CONFIRMATION MODAL
       ===================================== */

    .permission-delete-modal {
        z-index: 2000 !important;
    }

    .modal-backdrop.show {
        z-index: 1990 !important;
        opacity: 1 !important;
        background: rgba(2, 6, 23, 0.82) !important;
        backdrop-filter: blur(5px);
    }

    .permission-delete-modal .modal-dialog {
        max-width: 360px;
    }

    .permission-delete-modal .modal-content {
        background: linear-gradient(145deg, #14243b, #0c1729);
        color: #e5edf9;
        border: 1px solid #2583ff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.5);
    }

    .permission-delete-modal .modal-body {
        padding: 20px 22px;
        text-align: center;
    }

    .permission-delete-modal .warning-icon {
        width: 44px;
        height: 44px;
        border: 2px solid #dc3545;
        border-radius: 50%;
        color: #ff5b65;
        background: rgba(220, 53, 69, 0.10);
        font-size: 27px;
        font-weight: 700;
        line-height: 40px;
        margin: 0 auto 12px;
    }

    .permission-delete-modal .modal-title {
        font-size: 19px;
        font-weight: 700;
        color: #f8fafc;
        margin-bottom: 10px;
    }

    .permission-delete-modal .confirmation-text {
        color: #cbd5e1;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .permission-delete-modal .permission-user {
        display: block;
        color: #59a7ff;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 10px;
        overflow-wrap: anywhere;
    }

    .permission-delete-modal .warning-text {
        color: #94a3b8;
        font-size: 12px;
        margin-bottom: 16px;
    }

    .permission-delete-modal .modal-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .permission-delete-modal .modal-actions form {
        margin: 0;
    }

    .permission-delete-modal .btn-cancel {
        background: #192942 !important;
        border: 1px solid #59a7ff !important;
        color: #ffffff !important;
        border-radius: 8px;
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .permission-delete-modal .btn-cancel:hover {
        background: #203653 !important;
        color: #ffffff !important;
    }

    .permission-delete-modal .btn-confirm-delete {
        background: #dc3545 !important;
        border: 1px solid #dc3545 !important;
        color: #ffffff !important;
        border-radius: 8px;
        padding: 7px 14px;
        font-size: 13px;
        font-weight: 600;
    }

    .permission-delete-modal .btn-confirm-delete:hover {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
    }

    @media (max-width: 480px) {
        .permission-page .page-header {
            align-items: flex-start !important;
            gap: 12px;
        }

        .permission-delete-modal .modal-dialog {
            margin: 12px;
        }
    }
</style>

<div class="container mt-4 permission-page">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 page-header">
        <div>
            <h2>Share File</h2>
            <p class="text-muted mb-0">
                File: {{ $fileRecord->file_name }}
            </p>
        </div>

        <a href="{{ route('directories.show', [
            'virtual_disk' => $disk->id,
            'directory' => $folder->id
        ]) }}" class="btn btn-secondary">
            Back to Directory
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Grant Permissions Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <h5 class="mb-3">Grant File Permissions</h5>

            <form method="POST" action="{{ route('permissions.store', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id
            ]) }}">

                @csrf

                <div class="mb-3">
                    <label for="recipient_email" class="form-label">
                        Select Recipient
                    </label>

                    <select
                        name="recipient_email"
                        id="recipient_email"
                        class="form-select"
                        required
                    >
                        <option value="">Choose a user</option>

                        @foreach($users as $user)
                            <option
                                value="{{ $user->email }}"
                                {{ old('recipient_email') == $user->email ? 'selected' : '' }}
                            >
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Permissions</label>

                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="can_read"
                            value="1"
                            id="can_read"
                            class="form-check-input"
                            {{ old('can_read') ? 'checked' : '' }}
                        >
                        <label for="can_read" class="form-check-label">
                            Read - View and download the file
                        </label>
                    </div>

                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="can_write"
                            value="1"
                            id="can_write"
                            class="form-check-input"
                            {{ old('can_write') ? 'checked' : '' }}
                        >
                        <label for="can_write" class="form-check-label">
                            Write - Modify the file
                        </label>
                    </div>

                    <div class="form-check">
                        <input
                            type="checkbox"
                            name="can_execute"
                            value="1"
                            id="can_execute"
                            class="form-check-input"
                            {{ old('can_execute') ? 'checked' : '' }}
                        >
                        <label for="can_execute" class="form-check-label">
                            Execute - Execute the file (if supported)
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    Save Permissions
                </button>

            </form>

        </div>
    </div>

    {{-- Users With Access Card --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">Users With Access</h5>

            @forelse($permissions as $permission)

                <div class="access-item p-3 mb-2">

                    <strong>{{ $permission->user->name }}</strong>

                    <div class="text-muted small">
                        {{ $permission->user->email }}
                    </div>

                    <div class="mt-2">
                        @if($permission->can_read)
                            <span class="permission-badge">Read</span>
                        @endif

                        @if($permission->can_write)
                            <span class="permission-badge">Write</span>
                        @endif

                        @if($permission->can_execute)
                            <span class="permission-badge">Execute</span>
                        @endif
                    </div>

                    {{-- Delete Access Button --}}
                    <button
                        type="button"
                        class="btn btn-danger btn-sm mt-3"
                        onclick="openPermissionDeleteModal({{ $permission->id }})"
                    >
                        Delete Access
                    </button>

                    {{-- Delete Confirmation Modal --}}
                    <div
                        class="modal fade permission-delete-modal"
                        id="deletePermissionModal{{ $permission->id }}"
                        tabindex="-1"
                        aria-labelledby="deletePermissionLabel{{ $permission->id }}"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <div class="modal-body">

                                    <div class="warning-icon">!</div>

                                    <h5
                                        class="modal-title"
                                        id="deletePermissionLabel{{ $permission->id }}"
                                    >
                                        Confirm Delete Access
                                    </h5>

                                    <p class="confirmation-text">
                                        Are you sure you want to delete this user's
                                        permission to access this file?
                                    </p>

                                    <span class="permission-user">
                                        {{ $permission->user->name }}
                                    </span>

                                    <p class="warning-text">
                                        This action cannot be undone.
                                    </p>

                                    <div class="modal-actions">

                                        <button
                                            type="button"
                                            class="btn btn-cancel"
                                            data-bs-dismiss="modal"
                                        >
                                            Cancel
                                        </button>

                                        <form
                                            action="{{ route('permissions.destroy', [
                                                'virtual_disk' => $disk->id,
                                                'directory' => $folder->id,
                                                'file' => $fileRecord->id,
                                                'permission' => $permission->id
                                            ]) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-confirm-delete"
                                            >
                                                Yes, Delete
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            @empty

                <p class="text-muted mb-0">
                    This file has not been shared with anyone yet.
                </p>

            @endforelse

        </div>
    </div>

</div>

<script>
    function openPermissionDeleteModal(permissionId) {

        const modalElement = document.getElementById(
            'deletePermissionModal' + permissionId
        );

        if (!modalElement) {
            console.error('Permission modal not found:', permissionId);
            return;
        }

        // Move modal under body to avoid stacking conflicts.
        if (modalElement.parentElement !== document.body) {
            document.body.appendChild(modalElement);
        }

        // Open Bootstrap modal.
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    }
</script>

@endsection
