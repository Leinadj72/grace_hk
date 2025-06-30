<?php
require 'includes/db.php';
require 'includes/auth.php';
requireLogin();

if (!$_SESSION['is_paid']) {
    http_response_code(403);
    exit('Access denied.');
}

$file = $_GET['file'] ?? '';
$path = __DIR__ . '/uploads/private/' . basename($file);

if (!file_exists($path)) {
    http_response_code(404);
    exit('File not found.');
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);
finfo_close($finfo);

header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
