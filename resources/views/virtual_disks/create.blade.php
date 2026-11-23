
@extends('layout.app')

@section('content')

<div class="container py-4">

    <h2>Create Virtual Disk</h2>

    <form action="{{ route('virtual-disks.store') }}" method="POST">

        @csrf

        <div class="mb-3">
            <label class="form-label">Disk Name</label>

            <input type="text"
                   name="disk_name"
                   class="form-control"
                   value="{{ old('disk_name') }}"
                   placeholder="My First Disk"
                   required>
            @error('disk_name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Total Blocks</label>

            <input type="number"
                   name="total_blocks"
                   class="form-control"
                   value="{{ old('total_blocks', 100) }}"
                   min="1"
                   max="100000"
                   required>
            @error('total_blocks')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Block Size (Bytes)</label>

            <select name="block_size_bytes" class="form-select" required>
                <option value="512">512 Bytes</option>
                <option value="1024" selected>1024 Bytes (1 KB)</option>
                <option value="2048">2048 Bytes (2 KB)</option>
                <option value="4096">4096 Bytes (4 KB)</option>
            </select>
            @error('block_size_bytes')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Create Disk
        </button>

        <a href="{{ route('virtual-disks.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection
