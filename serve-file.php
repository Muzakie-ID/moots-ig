<?php
session_start();
require_once 'config/database.php';
require_once 'config/admin.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Forbidden');
}

// Get filename from query param
$filename = $_GET['file'] ?? '';

if (empty($filename) || preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
    http_response_code(400);
    exit('Invalid filename');
}

$filepath = 'upload-foto/' . $filename;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit('File not found');
}

// Get file info
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filepath);
finfo_close($finfo);

// Send headers
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));

// Serve file
readfile($filepath);
exit;
