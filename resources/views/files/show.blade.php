
@extends('layout.app')

@section('content')

<div class="container mt-4 file-show-page">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>View File</h2>
            <p class="text-muted mb-0">
                Virtual Disk: {{ $disk->disk_name }}
            </p>
        </div>

        @if(!empty($isShared))

            <a href="{{ route('permissions.shared') }}"
               class="btn btn-secondary">
                Back to Shared With Me
            </a>

        @else

            <a href="{{ route('directories.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id
            ]) }}"
               class="btn btn-secondary">
                Back to Directory
            </a>

        @endif
    </div>

    {{-- File Details --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3">
                <i class="bi bi-file-earmark-text me-2"
                   style="color: #3B82F6;"></i>
                {{ $fileRecord->file_name }}
            </h4>

            <div class="row mb-4">

                <div class="col-md-4">
                    <strong>File Type:</strong>
                    {{ strtoupper($fileRecord->extension ?? 'FILE') }}
                </div>

                <div class="col-md-4">
                    <strong>File Size:</strong>
                    {{ number_format($fileRecord->file_size_bytes) }} bytes
                </div>

                <div class="col-md-4">
                    <strong>Allocation:</strong>
                    {{ ucfirst($fileRecord->allocation_method) }}
                </div>

            </div>

            <hr>

            {{-- File Content --}}
            <h5 class="mb-3">File Content</h5>

            <div class="border rounded p-3 bg-light"
                 style="min-height: 300px; overflow-x: auto;">

                @if($previewAvailable && $isImage)

                    {{-- Image Preview --}}
                    @php
                        $physicalExtension = strtolower(
                            pathinfo($fileRecord->storage_path, PATHINFO_EXTENSION)
                        );

                        $imageMimeType = $imageMimeTypes[$physicalExtension]
                            ?? $imageMimeTypes[strtolower($fileRecord->extension ?? '')]
                            ?? 'image/jpeg';
                    @endphp

                    <div class="text-center">

                        <img
                            src="data:{{ $imageMimeType }};base64,{{ base64_encode($content) }}"
                            alt="{{ $fileRecord->file_name }}"
                            class="img-fluid rounded"
                            style="max-height: 600px;"
                        >

                    </div>

                @elseif($previewAvailable && $isDocx)

                    {{-- DOCX Preview --}}
                    <div class="mb-3">
                        <span class="badge bg-primary">
                            Word Document Preview
                        </span>
                    </div>

                    <pre style="white-space: pre-wrap; word-break: break-word; margin: 0; font-family: Arial, sans-serif;">{{ $content !== '' ? $content : 'This document contains no readable text.' }}</pre>

                @elseif($previewAvailable)

                    {{-- Supported Text File Preview --}}
                    <pre style="white-space: pre-wrap; word-break: break-word; margin: 0; font-family: monospace;">{{ $content !== '' ? $content : 'This file is empty.' }}</pre>

                @else

                    {{-- Unsupported File Preview --}}
                    <div class="text-center py-5">

                        <i class="bi bi-file-earmark-x"
                           style="font-size: 55px; color: #64748b;"></i>

                        <h5 class="mt-3">
                            Preview Unavailable
                        </h5>

                        <p class="text-muted mb-0">
                            {{ $previewMessage }}
                        </p>

                        <p class="text-muted small mt-2">
                            You can download the original file below.
                        </p>

                    </div>

                @endif

            </div>

            {{-- Actions --}}
            <div class="d-flex gap-2 mt-4 flex-wrap">

                @if(empty($isShared))

                    {{-- Owner Actions --}}

                    @if($canEdit)

                        <a href="{{ route('files.edit', [
                            'virtual_disk' => $disk->id,
                            'directory' => $folder->id,
                            'file' => $fileRecord->id
                        ]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit File
                        </a>

                    @endif

                    <a href="{{ route('files.download', [
                        'virtual_disk' => $disk->id,
                        'directory' => $folder->id,
                        'file' => $fileRecord->id
                    ]) }}"
                       class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>
                        Download File
                    </a>

                    <a href="{{ route('directories.show', [
                        'virtual_disk' => $disk->id,
                        'directory' => $folder->id
                    ]) }}"
                       class="btn btn-secondary">
                        Back
                    </a>

                @else

                    {{-- Shared User Actions --}}

                    @if($permission->can_write && $canEdit)

                        <a href="{{ route('shared-files.edit', [
                            'virtual_disk' => $disk->id,
                            'directory' => $folder->id,
                            'file' => $fileRecord->id
                        ]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i>
                            Edit File
                        </a>

                    @endif

                    @if($permission->can_read)

                        <a href="{{ route('shared-files.download', [
                            'virtual_disk' => $disk->id,
                            'directory' => $folder->id,
                            'file' => $fileRecord->id
                        ]) }}"
                           class="btn btn-primary">
                            <i class="bi bi-download me-1"></i>
                            Download File
                        </a>

                    @endif

                    <span class="text-muted align-self-center">
                        Shared file access
                    </span>

                    <a href="{{ route('permissions.shared') }}"
                       class="btn btn-secondary">
                        Back
                    </a>

                @endif

            </div>

        </div>
    </div>

</div>

<style>

    /* Primary Blue Buttons */

    .file-show-page .btn-primary {
        background-color: #2583ff !important;
        border-color: #2583ff !important;
        color: #ffffff !important;
    }

    .file-show-page .btn-primary:hover {
        background-color: #0869e8 !important;
        border-color: #0869e8 !important;
        color: #ffffff !important;
    }

    /* Secondary Dark Blue Buttons */

    .file-show-page .btn-secondary {
        background-color: #192942 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    .file-show-page .btn-secondary:hover {
        background-color: #203653 !important;
        border-color: #59a7ff !important;
        color: #ffffff !important;
    }

    /* Delete Buttons */

    .file-show-page .btn-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #ffffff !important;
    }

    .file-show-page .btn-danger:hover {
        background-color: #bb2d3b !important;
        border-color: #bb2d3b !important;
        color: #ffffff !important;
    }

    /* Button Appearance */

    .file-show-page .btn {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .file-show-page .btn:hover {
        transform: translateY(-2px);
    }

</style>

@endsection
