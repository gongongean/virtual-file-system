
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create File - Virtual File System</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 35px 20px;
            min-height: 100vh;
            background: #0B1220;
            font-family: Arial, sans-serif;
            color: #E2E8F0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #111827;
            padding: 35px;
            border: 1px solid #29364A;
            border-radius: 16px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.35);
        }

        /* HEADER */

        .page-header {
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 1px solid #29364A;
        }

        h2 {
            margin: 0 0 10px;
            color: #F8FAFC;
            font-size: 27px;
            font-weight: 700;
        }

        .description {
            color: #94A3B8;
            margin: 0;
            line-height: 1.6;
            font-size: 14px;
        }

        /* DISK INFORMATION */

        .disk-info {
            padding: 20px;
            margin-bottom: 28px;
            background: #17243A;
            border: 1px solid #354C83;
            border-radius: 12px;
            line-height: 1.9;
        }

        .disk-info-title {
            color: #60A5FA;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .disk-info strong {
            color: #CBD5E1;
        }

        .disk-info span {
            color: #F8FAFC;
        }

        /* FORM */

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #E2E8F0;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 13px 15px;
            background: #0B1220;
            border: 1px solid #3B506D;
            border-radius: 9px;
            color: #F8FAFC;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        input::placeholder,
        textarea::placeholder {
            color: #64748B;
        }

        select option {
            background: #111827;
            color: #F8FAFC;
        }

        /* FILENAME */

        .filename-row {
            display: flex;
            gap: 12px;
        }

        .filename-row input:first-child {
            flex: 3;
        }

        .filename-row input:last-child {
            flex: 1;
        }

        .help-text {
            display: block;
            color: #94A3B8;
            margin-top: 9px;
            font-size: 12px;
        }

        /* CONTENT EDITOR */

        textarea {
            min-height: 350px;
            resize: vertical;
            font-family: Consolas, monospace;
            line-height: 1.7;
            padding: 18px;
            tab-size: 4;
        }

        .editor-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 11px 15px;
            background: #1E293B;
            border: 1px solid #3B506D;
            border-bottom: none;
            border-radius: 9px 9px 0 0;
        }

        .editor-header span {
            font-size: 12px;
            color: #94A3B8;
        }

        .editor-header .editor-status {
            color: #60A5FA;
        }

        .editor-header + textarea {
            border-radius: 0 0 9px 9px;
        }

        /* ALLOCATION */

        .allocation-description {
            color: #94A3B8;
            font-size: 12px;
            margin-top: 9px;
            line-height: 1.5;
        }

        /* ERRORS */

        .alert {
            padding: 16px 18px;
            background: rgba(168, 60, 74, 0.14);
            color: #FCA5A5;
            border: 1px solid #A83C4A;
            border-radius: 10px;
            margin-bottom: 22px;
            font-size: 14px;
        }

        .alert ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .error {
            color: #FCA5A5;
            font-size: 13px;
            margin-top: 6px;
        }

        /* BUTTONS */

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 30px;
            padding-top: 22px;
            border-top: 1px solid #29364A;
        }

        button,
        .cancel-button {
            display: inline-block;
            padding: 12px 23px;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
        }

        .save-button {
            background: #315B96;
            color: white;
        }

        .save-button:hover {
            background: #3D70B5;
            transform: translateY(-2px);
        }

        .cancel-button {
            background: #29364A;
            color: #E2E8F0;
            border: 1px solid #3B506D;
        }

        .cancel-button:hover {
            background: #3B506D;
            color: white;
            transform: translateY(-2px);
        }

        /* RESPONSIVE */

        @media (max-width: 600px) {
            body {
                padding: 15px 10px;
            }

            .container {
                padding: 22px 18px;
            }

            h2 {
                font-size: 23px;
            }

            .filename-row {
                flex-direction: column;
            }

            .actions {
                flex-direction: column;
            }

            .actions button,
            .actions a {
                width: 100%;
            }

            textarea {
                min-height: 250px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="page-header">

        <h2>Create New File</h2>

        <p class="description">
            Create and save a text file directly inside your virtual disk.
        </p>

    </div>

    {{-- DISK INFORMATION --}}
    <div class="disk-info">

        <div class="disk-info-title">
            VIRTUAL DISK INFORMATION
        </div>

        <strong>Virtual Disk:</strong>
        <span>{{ $disk->disk_name }}</span>
        <br>

        <strong>Directory:</strong>
        <span>{{ $folder->directory_name }}</span>
        <br>

        <strong>Block Size:</strong>
        <span>{{ number_format($disk->block_size_bytes) }} bytes</span>

    </div>

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())

        <div class="alert">

            <strong>Please correct the following errors:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif

    {{-- CREATE FILE FORM --}}
    <form
        action="{{ route('files.store-created', [
            'virtual_disk' => $disk->id,
            'directory' => $folder->id
        ]) }}"
        method="POST"
    >

        @csrf

        {{-- FILE NAME --}}
        <div class="form-group">

            <label for="file_name">
                File Name
            </label>

            <div class="filename-row">

                <input
                    type="text"
                    id="file_name"
                    name="file_name"
                    placeholder="Enter filename"
                    value="{{ old('file_name') }}"
                    maxlength="230"
                    required
                >

                <input
                    type="text"
                    id="extension"
                    name="extension"
                    placeholder="txt"
                    value="{{ old('extension', 'txt') }}"
                    maxlength="20"
                >

            </div>

            <small class="help-text">
                Example: document + txt = document.txt
            </small>

        </div>

        {{-- FILE CONTENT --}}
        <div class="form-group">

            <label for="content">
                File Content
            </label>

            <div class="editor-header">

                <span>TEXT EDITOR</span>

                <span class="editor-status">
                    Ready to write
                </span>

            </div>

            <textarea
                id="content"
                name="content"
                placeholder="Start writing your file content here..."
            >{{ old('content') }}</textarea>

        </div>

        {{-- ALLOCATION METHOD --}}
        <div class="form-group">

            <label for="allocation_method">
                Block Allocation Method
            </label>

            <select
                id="allocation_method"
                name="allocation_method"
                required
            >

                <option
                    value="contiguous"
                    {{ old('allocation_method', 'contiguous') === 'contiguous' ? 'selected' : '' }}
                >
                    Contiguous Allocation
                </option>

                <option
                    value="linked"
                    {{ old('allocation_method') === 'linked' ? 'selected' : '' }}
                >
                    Linked Allocation
                </option>

            </select>

            <p class="allocation-description">
                Select how the file's data blocks will be organized
                inside your virtual disk.
            </p>

        </div>

        {{-- ACTION BUTTONS --}}
        <div class="actions">

            <button type="submit" class="save-button">
                Save File
            </button>

            <a
                href="{{ route('directories.show', [
                    'virtual_disk' => $disk->id,
                    'directory' => $folder->id
                ]) }}"
                class="cancel-button"
            >
                Cancel
            </a>

        </div>

    </form>

</div>

</body>
</html>
