
@extends('layout.app')

@section('content')

<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2>Directory Management</h2>
            <p class="text-muted mb-0">
                Virtual Disk: {{ $disk->disk_name }}
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">

            @if($currentDirectory)
                <a href="{{ route('directories.index', $disk->id) }}"
                   class="btn btn-primary">
                    Back to Root Directory
                </a>
            @endif

            <a href="{{ route('virtual-disks.show', $disk->id) }}"
               class="btn btn-secondary">
                Back to Virtual Disk
            </a>

        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- CURRENT DIRECTORY --}}
            <div class="mb-3">
                @if($currentDirectory)
                    <strong>{{ $currentDirectory->directory_name }}</strong>
                @else
                    <strong>Root Directory</strong>
                @endif
            </div>

            <hr>

            {{-- CREATE BUTTONS --}}
            <div class="mb-3 d-flex gap-2 flex-wrap">

                @if(!$currentDirectory)
                    <a href="{{ route('directories.create', [
                        'virtual_disk' => $disk->id,
                        'parent_id' => $currentDirectory?->id
                    ]) }}"
                       class="btn btn-primary">
                        Create Folder
                    </a>
                @endif

                @if($currentDirectory)
                    <a href="{{ route('files.create', [
                        'virtual_disk' => $disk->id,
                        'directory' => $currentDirectory->id
                    ]) }}"
                       class="btn btn-primary">
                        Create File
                    </a>
                @endif

            </div>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- FOLDERS --}}
            <h5>Folders</h5>

            @forelse($directories as $directory)

                <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">

                    <div>
                        <a href="{{ route('directories.show', [
                            'virtual_disk' => $disk->id,
                            'directory' => $directory->id
                        ]) }}"
                           class="text-decoration-none folder-link">

                            <i class="bi bi-folder2-open folder-icon me-2"></i>
                            <strong>{{ $directory->directory_name }}</strong>

                        </a>

                        <div class="text-muted small">Folder</div>
                    </div>

                    <button type="button"
                            class="btn btn-danger btn-sm"
                            onclick="openDeleteModal({{ $directory->id }})">
                        Delete
                    </button>

                </div>

                {{-- FOLDER DELETE MODAL --}}
                <div id="deleteModal{{ $directory->id }}"
                     class="delete-modal"
                     onclick="closeModalOnBackdrop(event, this)">

                    <div class="delete-modal-content folder-delete-content">

                        <div class="delete-icon">!</div>

                        <h4>Confirm Deletion</h4>

                        <p class="folder-delete-question">
                            Are you sure you want to delete this folder?
                        </p>

                        <div class="folder-name-box">
                            <i class="bi bi-folder2-open"></i>
                            <strong>{{ $directory->directory_name }}</strong>
                        </div>

                        <p class="folder-delete-note">
                            <i class="bi bi-info-circle"></i>
                            This action cannot be undone.
                        </p>

                        <div class="d-flex justify-content-center gap-2 mt-4">

                            <button type="button"
                                    class="btn btn-secondary"
                                    onclick="closeDeleteModal({{ $directory->id }})">
                                Cancel
                            </button>

                            <form action="{{ route('directories.destroy', [
                                'virtual_disk' => $disk->id,
                                'directory' => $directory->id
                            ]) }}"
                                  method="POST">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash3"></i>
                                    Yes, Delete
                                </button>

                            </form>

                        </div>

                    </div>
                </div>

            @empty

                <p class="text-muted">This directory has no folders.</p>

            @endforelse


            {{-- FILE MANAGEMENT --}}
            @if($currentDirectory)

                <hr>

                <h5 class="mb-3">Files</h5>

                {{-- UPLOAD FILE --}}
                <form action="{{ route('files.store', [
                    'virtual_disk' => $disk->id,
                    'directory' => $currentDirectory->id
                ]) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="border rounded p-3 mb-4">

                    @csrf

                    <label for="uploaded_file" class="form-label">
                        Upload a File
                    </label>

                    <input type="file"
                           name="uploaded_file"
                           id="uploaded_file"
                           class="form-control mb-3"
                           required>

                    <label for="allocation_method" class="form-label">
                        Allocation Method
                    </label>

                    <select name="allocation_method"
                            id="allocation_method"
                            class="form-select mb-3"
                            required>

                        <option value="contiguous"
                            {{ old('allocation_method', 'contiguous') == 'contiguous' ? 'selected' : '' }}>
                            Contiguous Allocation
                        </option>

                        <option value="linked"
                            {{ old('allocation_method') == 'linked' ? 'selected' : '' }}>
                            Linked Allocation
                        </option>

                    </select>

                    <button type="submit" class="btn btn-primary">
                        Upload File
                    </button>

                    <small class="text-muted d-block mt-2">
                        Maximum upload size: 10 MB.
                    </small>

                    @error('uploaded_file')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror

                    @error('allocation_method')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror

                </form>

                {{-- DISPLAY FILES --}}
                @forelse($files as $file)

                    <div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2">

                        <div>

                            <i class="bi bi-file-earmark-text file-icon me-2"></i>

                            <a href="{{ route('files.show', [
                                'virtual_disk' => $disk->id,
                                'directory' => $currentDirectory->id,
                                'file' => $file->id
                            ]) }}"
                               class="text-decoration-none fw-bold">
                                {{ $file->file_name }}
                            </a>

                            <div class="text-muted small">
                                {{ strtoupper($file->extension ?? 'FILE') }}
                                |
                                {{ number_format($file->file_size_bytes / 1024, 2) }} KB
                            </div>

                        </div>

                        <div class="d-flex gap-2 flex-wrap">

                            {{-- DOWNLOAD --}}
                            <a href="{{ route('files.download', [
                                'virtual_disk' => $disk->id,
                                'directory' => $currentDirectory->id,
                                'file' => $file->id
                            ]) }}"
                               class="btn btn-primary btn-sm">
                                Download
                            </a>

                            {{-- SHARE --}}
                            <a href="{{ route('permissions.create', [
                                'virtual_disk' => $disk->id,
                                'directory' => $currentDirectory->id,
                                'file' => $file->id
                            ]) }}"
                               class="btn btn-primary btn-sm">
                                Share
                            </a>

                            {{-- RENAME --}}
                            <button type="button"
                                    class="btn btn-primary btn-sm"
                                    onclick="openRenameModal({{ $file->id }})">
                                Rename
                            </button>

                            {{-- DELETE --}}
                            <button type="button"
                                    class="btn btn-danger btn-sm"
                                    onclick="openFileDeleteModal({{ $file->id }})">
                                Delete
                            </button>

                        </div>

                    </div>

                    {{-- FILE RENAME MODAL --}}
                    <div id="renameModal{{ $file->id }}"
                         class="delete-modal"
                         onclick="closeModalOnBackdrop(event, this)">

                        <div class="delete-modal-content rename-modal-content">

                            <h4>Rename File</h4>

                            <p class="text-muted">
                                Enter a new name for this file.
                            </p>

                            <form action="{{ route('files.rename', [
                                'virtual_disk' => $disk->id,
                                'directory' => $currentDirectory->id,
                                'file' => $file->id
                            ]) }}"
                                  method="POST">

                                @csrf
                                @method('PATCH')

                                <input type="text"
                                       name="file_name"
                                       class="form-control mb-3"
                                       value="{{ $file->file_name }}"
                                       maxlength="255"
                                       required>

                                <div class="d-flex justify-content-center gap-2">

                                    <button type="button"
                                            class="btn btn-secondary"
                                            onclick="closeRenameModal({{ $file->id }})">
                                        Cancel
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        Save Changes
                                    </button>

                                </div>

                            </form>

                        </div>
                    </div>

                    {{-- FILE DELETE MODAL --}}
                    <div id="fileDeleteModal{{ $file->id }}"
                         class="delete-modal"
                         onclick="closeModalOnBackdrop(event, this)">

                        <div class="delete-modal-content file-delete-content">

                            <div class="delete-icon">!</div>

                            <h4>Confirm Deletion</h4>

                            <p class="file-delete-question">
                                Are you sure you want to delete this file?
                            </p>

                            <div class="file-name-box">
                                <i class="bi bi-file-earmark-text"></i>
                                <strong>{{ $file->file_name }}</strong>
                            </div>

                            <p class="file-delete-note">
                                <i class="bi bi-info-circle"></i>
                                This action cannot be undone.
                            </p>

                            <div class="d-flex justify-content-center gap-2 mt-4">

                                <button type="button"
                                        class="btn btn-secondary"
                                        onclick="closeFileDeleteModal({{ $file->id }})">
                                    Cancel
                                </button>

                                <form action="{{ route('files.destroy', [
                                    'virtual_disk' => $disk->id,
                                    'directory' => $currentDirectory->id,
                                    'file' => $file->id
                                ]) }}"
                                      method="POST">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash3"></i>
                                        Yes, Delete
                                    </button>

                                </form>

                            </div>

                        </div>
                    </div>

                @empty

                    <p class="text-muted">No files in this directory.</p>

                @endforelse

            @endif

        </div>
    </div>

</div>


{{-- PAGE AND MODAL STYLING --}}
<style>

    /* BUTTON THEME */

    .container .btn {
        border-radius: 9px !important;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .container .btn-primary,
    .container .btn-success,
    .container .btn-info,
    .container .btn-warning {
        background: #2583ff !important;
        border-color: #2583ff !important;
        color: #ffffff !important;
    }

    .container .btn-primary:hover,
    .container .btn-success:hover,
    .container .btn-info:hover,
    .container .btn-warning:hover {
        background: #0869e8 !important;
        border-color: #0869e8 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
    }

    .container .btn-secondary {
        background: #192942 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    .container .btn-secondary:hover {
        background: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    .container .btn-danger {
        background: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .container .btn-danger:hover {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
    }

    /* ICONS */

    .folder-icon {
        color: #3B82F6;
        font-size: 22px;
        vertical-align: middle;
    }

    .folder-link {
        color: inherit;
    }

    .folder-link:hover .folder-icon {
        color: #60A5FA;
    }

    .file-icon {
        color: #60A5FA;
        font-size: 20px;
        vertical-align: middle;
    }

    /* FULL-PAGE MODAL OVERLAY */

    .delete-modal {
        display: none;
        position: fixed !important;
        inset: 0 !important;
        z-index: 999999 !important;
        width: 100vw !important;
        height: 100vh !important;
        min-height: 100vh;
        background: rgba(2, 6, 23, 0.86);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        justify-content: center;
        align-items: center;
        padding: 15px;
        box-sizing: border-box;
        overflow-y: auto;
    }

    /* GENERAL MODAL CONTENT */

    .delete-modal-content {
        position: relative;
        background: #111827;
        width: 100%;
        max-width: 370px;
        padding: 23px 25px;
        margin: auto;
        border: 1px solid #2563eb;
        border-radius: 18px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.55);
        text-align: center;
        animation: modalFadeIn 0.25s ease-in-out;
        flex-shrink: 0;
    }

    .delete-modal-content h4 {
        margin-bottom: 14px;
        color: #f8fafc;
        font-weight: 700;
    }

    .delete-modal-content p {
        color: #cbd5e1;
    }

    .delete-modal-content .text-muted {
        color: #94a3b8 !important;
    }

    .delete-modal-content strong {
        color: #60a5fa;
        overflow-wrap: anywhere;
    }

    /* WARNING ICON */

    .delete-icon {
        width: 58px;
        height: 58px;
        border: 3px solid #ef4444;
        color: #ef4444;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 20px;
        font-size: 32px;
        font-weight: bold;
    }

    /* FOLDER DELETE MODAL */

    .delete-modal-content.folder-delete-content {
        box-sizing: border-box;
        width: 100%;
        max-width: 465px;
        padding: 20px 25px;
        background: linear-gradient(145deg, #15263e 0%, #0d192b 100%);
        border: 1px solid #31577e;
        border-radius: 21px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55);
    }

    .folder-delete-content .delete-icon {
        width: 52px;
        height: 52px;
        font-size: 29px;
        margin-bottom: 14px;
    }

    .folder-delete-content h4 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 9px;
    }

    .folder-delete-question {
        color: #b8c7d9 !important;
        font-size: 14px;
        margin-bottom: 12px;
    }

    .folder-name-box {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
        width: 100%;
        min-height: 46px;
        padding: 9px 14px;
        margin: 12px 0;
        background: #1c304a;
        border: 1px solid #315174;
        border-radius: 12px;
        box-sizing: border-box;
        overflow-wrap: anywhere;
    }

    .folder-name-box i {
        color: #60a5fa;
        font-size: 18px;
        flex-shrink: 0;
    }

    .folder-name-box strong {
        color: #60a5fa !important;
        font-size: 15px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .folder-delete-note {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        color: #b8c7d9 !important;
        font-size: 13px;
        margin: 12px 0 0;
    }

    .folder-delete-note i {
        color: #fbbf24;
        font-size: 15px;
        flex-shrink: 0;
    }

    .folder-delete-content .btn {
        min-height: 40px;
        padding: 7px 15px;
        border-radius: 10px !important;
        font-size: 13px;
        font-weight: 600;
    }

    .folder-delete-content .btn-secondary {
        background: #192f4a !important;
        border: 1px solid #4d8dc8 !important;
        color: #ffffff !important;
    }

    .folder-delete-content .btn-secondary:hover {
        background: #294568 !important;
        border-color: #65aaf0 !important;
    }

    .folder-delete-content .btn-danger {
        background: #dc3545 !important;
        border: 1px solid #dc3545 !important;
        color: #ffffff !important;
    }

    .folder-delete-content .btn-danger:hover {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
    }

    /* FILE DELETE MODAL */

    .delete-modal-content.file-delete-content {
        box-sizing: border-box;
        width: 100%;
        max-width: 430px;
        padding: 30px;
        background: linear-gradient(145deg, #192d48 0%, #0d192b 100%);
        border: 1px solid #31577e;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.55);
    }

    .file-delete-content h4 {
        color: #ffffff;
        font-size: 23px;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .file-delete-question {
        color: #cbd5e1 !important;
        font-size: 15px;
        margin-bottom: 18px;
    }

    .file-name-box {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        min-height: 54px;
        padding: 13px 16px;
        margin: 18px 0;
        background: #213a58;
        border: 1px solid #39618b;
        border-radius: 12px;
        box-sizing: border-box;
        overflow-wrap: anywhere;
    }

    .file-name-box i {
        color: #60a5fa;
        font-size: 20px;
        flex-shrink: 0;
    }

    .file-name-box strong {
        color: #60a5fa !important;
        font-size: 15px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .file-delete-note {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        color: #b8c7d9 !important;
        font-size: 14px;
        margin: 18px 0 0;
    }

    .file-delete-note i {
        color: #fbbf24;
        font-size: 16px;
        flex-shrink: 0;
    }

    .file-delete-content .btn {
        min-height: 43px;
        padding: 9px 17px;
        border-radius: 10px !important;
        font-size: 14px;
        font-weight: 600;
    }

    .file-delete-content .btn-secondary {
        background: #192f4a !important;
        border: 1px solid #4d8dc8 !important;
        color: #ffffff !important;
    }

    .file-delete-content .btn-secondary:hover {
        background: #294568 !important;
        border-color: #65aaf0 !important;
    }

    .file-delete-content .btn-danger {
        background: #dc3545 !important;
        border: 1px solid #dc3545 !important;
        color: #ffffff !important;
    }

    .file-delete-content .btn-danger:hover {
        background: #bb2d3b !important;
        border-color: #bb2d3b !important;
    }

    /* RENAME MODAL */

    .rename-modal-content {
        max-width: 400px;
        background: linear-gradient(145deg, #192d48 0%, #0d192b 100%);
        border: 1px solid #31577e;
    }

    .rename-modal-content .btn {
        border-radius: 10px !important;
        font-weight: 600;
    }

    .rename-modal-content .btn-secondary {
        background: #192f4a !important;
        border: 1px solid #4d8dc8 !important;
        color: #ffffff !important;
    }

    .rename-modal-content .btn-primary {
        background: #2583ff !important;
        border: 1px solid #2583ff !important;
        color: #ffffff !important;
    }

    /* RESPONSIVE MODALS */

    @media (max-width: 576px) {

        .delete-modal-content.folder-delete-content,
        .delete-modal-content.file-delete-content {
            max-width: 100%;
            padding: 25px 18px;
        }

        .folder-delete-content h4,
        .file-delete-content h4 {
            font-size: 21px;
        }

        .folder-delete-content .btn,
        .file-delete-content .btn {
            padding: 8px 12px;
            font-size: 13px;
        }

    }

    /* COMPACT CONFIRMATION MODALS */

    .delete-modal-content.folder-delete-content,
    .delete-modal-content.file-delete-content {
        box-sizing: border-box;
        max-width: 400px;
        padding: 17px 22px;
    }

    .folder-delete-content .delete-icon,
    .file-delete-content .delete-icon {
        width: 46px;
        height: 46px;
        font-size: 26px;
        margin-bottom: 12px;
    }

    .folder-delete-content h4,
    .file-delete-content h4 {
        font-size: 20px;
        margin-bottom: 8px;
    }

    .folder-delete-question,
    .file-delete-question {
        font-size: 13px;
        margin-bottom: 10px;
    }

    .folder-name-box,
    .file-name-box {
        min-height: 42px;
        padding: 8px 12px;
        margin: 10px 0;
    }

    .folder-delete-note,
    .file-delete-note {
        font-size: 12px;
        margin-top: 10px;
    }

    .folder-delete-content .btn,
    .file-delete-content .btn {
        min-height: 36px;
        padding: 6px 13px;
        font-size: 12px;
    }

    /* MOBILE MODAL SIZE */

    @media (max-width: 576px) {
        .delete-modal-content.folder-delete-content,
        .delete-modal-content.file-delete-content {
            max-width: 100%;
            padding: 17px 18px;
        }
    }

    /* ANIMATION */

    @keyframes modalFadeIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(10px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

</style>


{{-- MODAL JAVASCRIPT --}}
<script>

    // OPEN MODAL
    function showModal(modalId) {

        const modal = document.getElementById(modalId);

        if (!modal) {
            console.error('Modal not found:', modalId);
            return;
        }

        /*
         * Move modal directly into BODY.
         * This prevents parent stacking contexts from
         * restricting the full-page blur overlay.
         */
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        modal.style.display = 'flex';

    }

    // CLOSE MODAL
    function hideModal(modalId) {

        const modal = document.getElementById(modalId);

        if (modal) {
            modal.style.display = 'none';
        }

    }

    // CLOSE WHEN CLICKING OUTSIDE THE MODAL
    function closeModalOnBackdrop(event, modal) {

        if (event.target === modal) {
            modal.style.display = 'none';
        }

    }

    // FOLDER DELETION
    function openDeleteModal(folderId) {
        showModal('deleteModal' + folderId);
    }

    function closeDeleteModal(folderId) {
        hideModal('deleteModal' + folderId);
    }

    // FILE DELETION
    function openFileDeleteModal(fileId) {
        showModal('fileDeleteModal' + fileId);
    }

    function closeFileDeleteModal(fileId) {
        hideModal('fileDeleteModal' + fileId);
    }

    // FILE RENAMING
    function openRenameModal(fileId) {
        showModal('renameModal' + fileId);
    }

    function closeRenameModal(fileId) {
        hideModal('renameModal' + fileId);
    }

</script>

@endsection
