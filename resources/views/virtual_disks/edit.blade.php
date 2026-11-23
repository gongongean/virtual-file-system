
@extends('layout.app')

@section('content')

<div class="container py-4">

    <h2>Edit Virtual Disk</h2>

    <form action="{{ route('virtual-disks.update', $disk->id) }}"
          method="POST">

        @csrf
        @method('PUT')

        {{-- Disk Name --}}
        <div class="mb-3">
            <label class="form-label">Disk Name</label>

            <input type="text"
                   name="disk_name"
                   class="form-control"
                   value="{{ old('disk_name', $disk->disk_name) }}"
                   required>
                   
            @error('disk_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Total Blocks --}}
        <div class="mb-3">
            <label class="form-label">Total Blocks</label>

            <input type="number"
                   name="total_blocks"
                   class="form-control"
                   value="{{ old('total_blocks', $disk->total_blocks) }}"
                   min="{{ $disk->total_blocks }}"
                   max="100000"
                   required>

            <small class="text-muted">
                Increase the number of blocks to expand disk capacity.
                Current capacity:
                {{ number_format($disk->total_blocks) }} blocks.
            </small>

            @error('total_blocks')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        {{-- Block Size --}}
        <div class="mb-3">
            <label class="form-label">Block Size</label>

            <input type="text"
                   class="form-control"
                   value="{{ number_format($disk->block_size_bytes) }} bytes"
                   readonly>

            <small class="text-muted">
                Block size cannot be changed after disk creation.
            </small>
        </div>

        <button type="submit" class="btn btn-primary">
            Update Disk
        </button>

        <a href="{{ route('virtual-disks.show', $disk->id) }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection
