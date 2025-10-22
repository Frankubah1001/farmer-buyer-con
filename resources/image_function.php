<?php

function uploadImages(array $files, string $uploadDir, array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], int $maxSize = 2097152): array
{
    $uploadedPaths = [];
    // Ensure the upload directory exists and is writable
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['status' => 'error', 'message' => 'Failed to create upload directory.'];
        }
    } elseif (!is_writable($uploadDir)) {
        return ['status' => 'error', 'message' => 'Upload directory is not writable.'];
    }

    foreach ($files['tmp_name'] as $key => $tmpName) {
        $fileName = basename($files['name'][$key]);
        $targetPath = $uploadDir . $fileName;
        $fileType = $files['type'][$key];
        $fileSize = $files['size'][$key];
        $fileError = $files['error'][$key];

        if ($fileError === UPLOAD_ERR_OK) {
            if (in_array($fileType, $allowedTypes)) {
                if ($fileSize <= $maxSize) {
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $uploadedPaths[] = $targetPath;
                    } else {
                        return ['status' => 'error', 'message' => 'Failed to move uploaded file: ' . $fileName];
                    }
                } else {
                    return ['status' => 'error', 'message' => 'File size exceeds the limit (' . formatBytes($maxSize) . '): ' . $fileName];
                }
            } else {
                return ['status' => 'error', 'message' => 'Invalid file type. Allowed types are: ' . implode(', ', array_map(function($type){ return str_replace('image/', '', $type); }, $allowedTypes)) . ' for file: ' . $fileName];
            }
        } elseif ($fileError !== UPLOAD_ERR_NO_FILE) {
            return ['status' => 'error', 'message' => 'Error during file upload (' . $fileError . ') for file: ' . $fileName];
        }
    }

    return ['status' => 'success', 'paths' => $uploadedPaths];
}

function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes, 1024) : 0));
    $pow = min($pow, count($units) - 1);
    $bytes /= (1 << (10 * $pow));
    return round($bytes, $precision) . ' ' . $units[$pow];
}

?>