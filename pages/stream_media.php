<?php
require '../includes/auth.php';
requireLogin();

if (!$_SESSION['is_paid']) {
    http_response_code(403);
    exit('Access denied');
}

// Sanitize and validate filename
if (!isset($_GET['file'])) {
    http_response_code(400);
    exit('No file specified.');
}

$file = basename($_GET['file']);
$path = "../uploads/private/" . $file;

// Check if file exists
if (!file_exists($path)) {
    http_response_code(404);
    exit('File not found.');
}

// Get file mime type
$mime = mime_content_type($path);
header("Content-Type: $mime");
header("Content-Length: " . filesize($path));
header("Content-Disposition: inline; filename=\"$file\"");

// Stream the file
readfile($path);
exit;
