<?php
session_start();
require_once 'config/database.php';
require_once 'config/admin.php';

// Get filename from query param
$filename = $_GET['file'] ?? '';

if (empty($filename) || preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
    http_response_code(400);
    exit('Invalid filename');
}

$filepath = __DIR__ . '/upload-foto/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit('File not found');
}

// Check if admin is logged in OR member is logged in
$isAdmin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
$isMember = isset($_SESSION['username']);

if (!$isAdmin && !$isMember) {
    http_response_code(403);
    exit('Forbidden');
}

// If member is logged in, they can only access their own file
if ($isMember && !$isAdmin) {
    $loggedInUsername = $_SESSION['username'];
    $fileUsername = pathinfo($filename, PATHINFO_FILENAME);
    if ($loggedInUsername !== $fileUsername) {
        http_response_code(403);
        exit('Forbidden');
    }
}

// Get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filepath);
finfo_close($finfo);

// Send headers
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));

// Serve file
readfile($filepath);
exit;
