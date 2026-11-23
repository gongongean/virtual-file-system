<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;
use ZipArchive;
use Throwable;
use RuntimeException;

class FilePreviewService
{
    private array $imageMimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jfif' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'bmp' => 'image/bmp',
    ];

    private array $textExtensions = [
        'txt', 'md', 'csv', 'log', 'json',
        'xml', 'html', 'css', 'js', 'php',
    ];

    public function preview(File $fileRecord): array
    {
        // Actual physical storage extension.
        $physicalExtension = strtolower(
            pathinfo($fileRecord->storage_path, PATHINFO_EXTENSION)
        );

        // Logical extension saved in the database.
        $extension = strtolower($fileRecord->extension ?? '');

        // Preview detection uses the physical extension.
        $isImage = isset($this->imageMimeTypes[$physicalExtension]);
        $isDocx = $physicalExtension === 'docx';

        // Editing detection uses the database extension.
        $canEdit = in_array(
            $extension,
            $this->textExtensions,
            true
        );

        $result = [
            'content' => '',
            'isImage' => $isImage,
            'isDocx' => $isDocx,
            'canEdit' => $canEdit,
            'imageMimeTypes' => $this->imageMimeTypes,
            'previewAvailable' => false,
            'previewMessage' => 'Preview is not available for this file type. Download the file to open it.',
        ];

        try {
            $disk = Storage::disk('local');

            if (!$disk->exists($fileRecord->storage_path)) {
                throw new RuntimeException(
                    'Original file not found: ' . $fileRecord->storage_path
                );
            }

            if ($isImage) {

                $result['content'] = $disk->get(
                    $fileRecord->storage_path
                );

                $result['previewAvailable'] = true;
                $result['previewMessage'] = '';

            } elseif ($isDocx) {

                $path = $disk->path(
                    $fileRecord->storage_path
                );

                $result['content'] = $this->extractDocxText($path);

                $result['previewAvailable'] = true;
                $result['previewMessage'] = '';

            } elseif ($canEdit) {

                $result['content'] = $disk->get(
                    $fileRecord->storage_path
                );

                $result['previewAvailable'] = true;
                $result['previewMessage'] = '';
            }

        } catch (Throwable $exception) {

            Log::error('FILE PREVIEW DIAGNOSTICS', [
                'error' => $exception->getMessage(),
                'php_version' => PHP_VERSION,
                'php_sapi' => PHP_SAPI,
                'php_ini' => php_ini_loaded_file(),
                'extension_dir' => ini_get('extension_dir'),
                'zip_extension_loaded' => extension_loaded('zip'),
                'zip_class_exists' => class_exists(ZipArchive::class),
                'dom_extension_loaded' => extension_loaded('dom'),
                'dom_class_exists' => class_exists(DOMDocument::class),
                'xpath_class_exists' => class_exists(DOMXPath::class),
                'storage_path' => $fileRecord->storage_path,
                'physical_extension' => $physicalExtension,
                'database_extension' => $extension,
            ]);

            $result['content'] = '';
            $result['previewAvailable'] = false;
            $result['previewMessage'] =
                'Unable to preview this file. Please download the original file.';
        }

        return $result;
    }

    private function extractDocxText(string $path): string
    {
        if (!extension_loaded('zip') || !class_exists(ZipArchive::class)) {
            throw new RuntimeException(
                'PHP ZIP extension is not available.'
            );
        }

        if (!class_exists(DOMDocument::class)) {
            throw new RuntimeException(
                'PHP DOM extension is not available.'
            );
        }

        if (!file_exists($path)) {
            throw new RuntimeException(
                'DOCX physical file does not exist: ' . $path
            );
        }

        $zip = new ZipArchive();

        $openResult = $zip->open($path);

        if ($openResult !== true) {
            throw new RuntimeException(
                'Unable to open DOCX file. ZIP error code: ' . $openResult
            );
        }

        try {
            $xml = $zip->getFromName('word/document.xml');

            if ($xml === false) {
                throw new RuntimeException(
                    'DOCX document.xml content not found.'
                );
            }

            $document = new DOMDocument();

            $previousSetting = libxml_use_internal_errors(true);

            try {
                $loaded = $document->loadXML(
                    $xml,
                    LIBXML_NONET | LIBXML_NOBLANKS
                );

                if (!$loaded) {
                    $errors = libxml_get_errors();

                    $errorMessage = !empty($errors)
                        ? trim($errors[0]->message)
                        : 'Unknown XML parsing error.';

                    throw new RuntimeException(
                        'Invalid DOCX XML: ' . $errorMessage
                    );
                }

            } finally {
                libxml_clear_errors();
                libxml_use_internal_errors($previousSetting);
            }

            $xpath = new DOMXPath($document);

            $xpath->registerNamespace(
                'w',
                'http://schemas.openxmlformats.org/wordprocessingml/2006/main'
            );

            $paragraphs = $xpath->query('//w:p');

            if ($paragraphs === false) {
                throw new RuntimeException(
                    'Unable to read DOCX paragraphs.'
                );
            }

            $lines = [];

            foreach ($paragraphs as $paragraph) {

                $parts = $xpath->query(
                    './/w:t | .//w:tab | .//w:br | .//w:cr',
                    $paragraph
                );

                if ($parts === false) {
                    continue;
                }

                $line = '';

                foreach ($parts as $part) {

                    switch ($part->localName) {

                        case 't':
                            $line .= $part->textContent;
                            break;

                        case 'tab':
                            $line .= "\t";
                            break;

                        case 'br':
                        case 'cr':
                            $line .= "\n";
                            break;
                    }
                }

                $lines[] = $line;
            }

            return implode(PHP_EOL, $lines);

        } finally {
            $zip->close();
        }
    }
}
