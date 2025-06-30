<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin();

if (!$_SESSION['is_admin']) {
    exit("Access denied.");
}

$message = '';
$allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'video/mp4'];

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT * FROM media WHERE id = ?");
    $stmt->execute([$id]);
    $file = $stmt->fetch();

    if ($file) {
        $path = "../uploads/{$file['access']}/{$file['file_name']}";
        if (file_exists($path)) {
            unlink($path);
        }
        $pdo->prepare("DELETE FROM media WHERE id = ?")->execute([$id]);
        $message = "✅ Media deleted successfully.";
    } else {
        $message = "❌ File not found.";
    }
}

// Handle upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['media'], $_POST['access'])) {
    $file = $_FILES['media'];
    $access = $_POST['access'];

    if ($file['error'] === 0 && in_array($file['type'], $allowed_types)) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $type = str_starts_with($file['type'], 'image') ? 'image' : 'video';

        $dir = ($access === 'private') ? '../uploads/private/' : '../uploads/public/';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = uniqid() . '.' . $ext;
        $filepath = $dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
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

// Fetch all media
$stmt = $pdo->query("SELECT * FROM media ORDER BY uploaded_at DESC");
$mediaFiles = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Media Uploads</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/icon.jpeg">
    <style>
        video,
        img {
            max-width: 100px;
            max-height: 80px;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <div class="d-flex">
                <a href="../pages/logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="mb-4">📤 Upload & Manage Media</h3>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="mb-5 bg-white p-4 rounded shadow-sm">
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

        <h4 class="mb-3">📁 Uploaded Files</h4>
        <?php if (count($mediaFiles) === 0): ?>
            <p class="text-muted">No media uploaded yet.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Preview</th>
                            <th>Type</th>
                            <th>Access</th>
                            <th>Filename</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mediaFiles as $i => $file): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <?php if ($file['file_type'] === 'image'): ?>
                                        <img src="../uploads/<?= $file['access'] ?>/<?= $file['file_name'] ?>" alt="img">
                                    <?php else: ?>
                                        <video src="../uploads/<?= $file['access'] ?>/<?= $file['file_name'] ?>" controls muted></video>
                                    <?php endif; ?>
                                </td>
                                <td><?= ucfirst($file['file_type']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $file['access'] === 'private' ? 'danger' : 'success' ?>">
                                        <?= ucfirst($file['access']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($file['file_name']) ?></td>
                                <td><?= date('Y-m-d H:i', strtotime($file['uploaded_at'])) ?></td>
                                <td>
                                    <a href="?delete=<?= $file['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this file?')">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</body>

</html>