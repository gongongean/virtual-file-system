@extends('layout.app')

@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Edit File Content</h2>
            <p class="text-muted mb-0">
                Virtual Disk: {{ $disk->disk_name }}
            </p>
        </div>

        @if(!empty($isShared))
            <a href="{{ route('shared-files.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id
            ]) }}" class="btn btn-secondary">
                Back to File
            </a>
        @else
            <a href="{{ route('files.show', [
                'virtual_disk' => $disk->id,
                'directory' => $folder->id,
                'file' => $fileRecord->id
            ]) }}" class="btn btn-secondary">
                Back to File
            </a>
        @endif
    </div>

    {{-- Editor --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <h5 class="mb-3">
                📄 {{ $fileRecord->file_name }}
            </h5>

            <div class="mb-3">
                <strong>Current File Size:</strong>
                {{ number_format($fileRecord->file_size_bytes) }} bytes
            </div>

            @if(!empty($isShared))
                <form method="POST" action="{{ route('shared-files.update', [
                    'virtual_disk' => $disk->id,
                    'directory' => $folder->id,
                    'file' => $fileRecord->id
                ]) }}">
            @else
                <form method="POST" action="{{ route('files.update', [
                    'virtual_disk' => $disk->id,
                    'directory' => $folder->id,
                    'file' => $fileRecord->id
                ]) }}">
            @endif

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="content" class="form-label">
                        File Content
                    </label>

                    <textarea
                        name="content"
                        id="content"
                        class="form-control font-monospace"
                        rows="16"
                        style="resize: vertical;"
                    >{{ old('content', $content) }}</textarea>

                    @error('content')
                        <div class="text-danger mt-2">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex gap-2 flex-wrap">

                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>

                    @if(!empty($isShared))
                        <a href="{{ route('shared-files.show', [
                            'virtual_disk' => $disk->id,
                            'directory' => $folder->id,
                            'file' => $fileRecord->id
                        ]) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    @else
                        <a href="{{ route('files.show', [
                            'virtual_disk' => $disk->id,
                            'directory' => $folder->id,
                            'file' => $fileRecord->id
                        ]) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    @endif

                </div>

            </form>

        </div>
    </div>

</div>

@endsection
