<?php
require '../includes/db.php';

// Fetch public media
$stmt = $pdo->prepare("SELECT * FROM media WHERE access = 'public' ORDER BY uploaded_at DESC");
$stmt->execute();
$media = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Public Gallery</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/icon.jpeg">
    <style>
        .gallery-item {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Ghana Escorts</a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3 class="mb-4 text-center">Public Gallery</h3>
        <div class="row g-4">

            <?php if (count($media) === 0): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">No public media available yet.</div>
                </div>
            <?php else: ?>
                <?php foreach ($media as $file): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <?php if ($file['file_type'] === 'image'): ?>
                                <img src="../uploads/public/<?= htmlspecialchars($file['file_name']) ?>" class="gallery-item card-img-top" alt="Public Image">
                            <?php elseif ($file['file_type'] === 'video'): ?>
                                <video controls class="gallery-item card-img-top">
                                    <source src="../uploads/public/<?= htmlspecialchars($file['file_name']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

</body>

</html>