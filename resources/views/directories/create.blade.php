
@extends('layout.app')

@section('content')

<div class="container mt-4">

    <div class="mb-4">
        <h2>Create New Folder</h2>

        <p class="text-muted">
            Virtual Disk: {{ $disk->disk_name }}
        </p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <form action="{{ route('directories.store', $disk->id) }}"
                  method="POST">

                @csrf

                {{-- Parent Directory --}}
                <input type="hidden"
                       name="parent_id"
                       value="{{ $parentDirectory?->id }}">

                {{-- Folder Name --}}
                <div class="mb-3">

                    <label for="directory_name" class="form-label">
                        Folder Name
                    </label>

                    <input
                        type="text"
                        name="directory_name"
                        id="directory_name"
                        class="form-control @error('directory_name') is-invalid @enderror"
                        placeholder="Enter folder name"
                        value="{{ old('directory_name') }}"
                        required
                    >

                    @error('directory_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- Buttons --}}
                <button type="submit" class="btn btn-primary">
                    Create Folder
                </button>

                <a href="{{ route('directories.index', [
                    'virtual_disk' => $disk->id,
                    'parent_id' => $parentDirectory?->id
                ]) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

@endsection
