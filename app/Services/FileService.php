<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function upload_file(UploadedFile $file): string
    {
        try {
            if (!$file->isValid()) {
                throw new Exception('Invalid file uploaded');
            }

            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . uniqid() . '.' . $extension;

            $destination = public_path('uploads');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Move file into public/uploads
            $file->move($destination, $filename);

            // Return full URL (not just relative path)
            return url('uploads/' . $filename);

        } catch (Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            throw new Exception('File upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete file from storage
     */
    public function delete_file($filePath): bool
    {
        try {
            // If it's a full URL, extract the path
            if (filter_var($filePath, FILTER_VALIDATE_URL)) {
                $parsedUrl = parse_url($filePath);
                $filePath = $parsedUrl['path'] ?? $filePath;
            }

            $fullPath = public_path($filePath);
            
            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
            
            return false;
        } catch (Exception $e) {
            Log::error('File deletion failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file information
     */
    public function getFileInfo(string $fileUrl): array
    {
        $path = $this->extractFilePathFromUrl($fileUrl);
        $fullPath = public_path($path);

        if (!file_exists($fullPath)) {
            return [];
        }

        return [
            'url' => $fileUrl,
            'path' => $path,
            'size' => filesize($fullPath),
            'last_modified' => filemtime($fullPath),
            'exists' => true,
        ];
    }

    protected function extractFilePathFromUrl(string $url): string
    {
        $parsedUrl = parse_url($url);
        return $parsedUrl['path'] ?? $url;
    }
}