<?php
// Script to copy music files from YuE output directory to web-accessible directory

// Define source and destination directories
$sourceDir = 'C:\\Users\\Ahmed\\YuE-exllamav2\\output';
$destDir = __DIR__ . '/uploads/music';

// Ensure destination directory exists
if (!is_dir($destDir)) {
    mkdir($destDir, 0755, true);
}

// Get list of audio files in source directory
$copiedFiles = [];
$errorMessages = [];

if (is_dir($sourceDir)) {
    $files = scandir($sourceDir);
    
    foreach ($files as $file) {
        // Only copy .wav or .mp3 files
        if (pathinfo($file, PATHINFO_EXTENSION) === 'wav' || 
            pathinfo($file, PATHINFO_EXTENSION) === 'mp3') {
            
            $sourcePath = $sourceDir . '\\' . $file;
            $destPath = $destDir . '/' . $file;
            
            // Check if file exists and is readable
            if (file_exists($sourcePath) && is_readable($sourcePath)) {
                // Copy file if it doesn't exist or is newer
                if (!file_exists($destPath) || filemtime($sourcePath) > filemtime($destPath)) {
                    if (copy($sourcePath, $destPath)) {
                        $copiedFiles[] = $file;
                    } else {
                        $errorMessages[] = "Failed to copy file: {$file}";
                    }
                }
            } else {
                $errorMessages[] = "Source file not found or not readable: {$file}";
            }
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'copied_files' => $copiedFiles,
    'errors' => $errorMessages
]);
?>
