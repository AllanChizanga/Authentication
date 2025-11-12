<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Exception;

class FileService
{
public function upload_file($file): string
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
        \Log::error('File upload failed: ' . $e->getMessage());
        throw new Exception('File upload failed: ' . $e->getMessage());
    }
}


    public function upload($file)
{
    // Define the target directory inside "public/uploads"
    $uploadDir = __DIR__ . '/../public/uploads/';
    
    // Make sure the directory exists
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate a unique name for the file
    $fileName = uniqid() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;

    // Move file from /tmp to public/uploads
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Save relative path to DB (so frontend can access it)
        return '/uploads/' . $fileName;
    } else {
        throw new Exception('File upload failed.');
    }
}
    /**
     * Delete file from storage
     */
    public function delete_file($filePath): bool
    {
        try {
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
}