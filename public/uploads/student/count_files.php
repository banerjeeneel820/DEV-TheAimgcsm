<?php

$directory = __DIR__; // Replace with the actual path to your folder

// Check if the directory exists
if (is_dir($directory)) {
    // Get the list of files in the directory
    $files = scandir($directory);

    // Exclude "." and ".." from the list
    $files = array_diff($files, array('.', '..'));

    // Count the number of files
    $totalFiles = count($files);

    echo "Total files in the directory: $totalFiles";
} else {
    echo "The specified directory does not exist.";
}
