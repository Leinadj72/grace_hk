<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin();

if (!$_SESSION['is_admin']) {
    exit("Access denied.");
}

$message = '';
$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'], $_POST['access'])) {
    $file = $_FILES['media'];
    $access = $_POST['access'];

    if ($file['error'] === 0 && in_array($file['type'], $allowed_types)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $type = str_starts_with($file['type'], 'image') ? 'image' : 'video';

        $dir = ($access === 'private') ? '../uploads/private/' : '../uploads/public/';
        $filename = uniqid() . '.' . $ext;
        $filepath = $dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Save in database
            $stmt = $pdo->prepare("INSERT INTO media (file_name, file_type, access) VALUES (?, ?, ?)");
            $stmt->execute([$filename, $type, $access]);
            $message = "✅ File uploaded successfully!";
        } else {
            $message = "❌ Failed to move uploaded file.";
        }
    } else {
        $message = "❌ Invalid file type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upload Media</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <div class="d-flex">
                <a href="../logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3>📤 Upload Media</h3>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Choose File (Image or Video)</label>
                <input type="file" name="media" accept="image/*,video/mp4" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Access Level</label>
                <select name="access" class="form-select" required>
                    <option value="private">Private (paid users only)</option>
                    <option value="public">Public (everyone)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

</body>

</html>