<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

// Redirect free users
if (!$_SESSION['is_paid']) {
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Private Gallery</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" href="../assets/images/icon.jpeg">
    </head>
    <body class="bg-light" oncontextmenu="return false;">
        <div class="container mt-5">
            <div class="alert alert-danger text-center">
                🔒 This gallery is for paid users only.<br><br>
                <a href="upgrade.php" class="btn btn-warning">Upgrade Now</a>
            </div>
        </div>
        <script>
            document.addEventListener("contextmenu", e => e.preventDefault());
        </script>
        <script src="../assets/js/protection.js"></script>
    </body>
    </html>
    HTML;
    exit();
}

// Fetch media
$stmt = $pdo->prepare("SELECT * FROM media WHERE access = 'private' ORDER BY uploaded_at DESC");
$stmt->execute();
$media = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Private Gallery</title>
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

<body class="bg-light" oncontextmenu="return false;">

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
        <h3 class="mb-4 text-center">Private Gallery</h3>
        <div class="row g-4">
            <?php if (count($media) === 0): ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">No media uploaded yet.</div>
                </div>
            <?php else: ?>
                <?php foreach ($media as $file): ?>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <?php if ($file['file_type'] === 'image'): ?>
                                <img src="stream_media.php?file=<?= urlencode($file['file_name']) ?>"
                                    class="gallery-item card-img-top"
                                    alt="Gallery Image"
                                    oncontextmenu="return false;"
                                    data-bs-toggle="modal"
                                    data-bs-target="#imageModal"
                                    data-image="stream_media.php?file=<?= urlencode($file['file_name']) ?>">
                            <?php elseif ($file['file_type'] === 'video'): ?>
                                <video class="gallery-item card-img-top"
                                    muted
                                    controls
                                    oncontextmenu="return false;"
                                    controlsList="nodownload nofullscreen noremoteplayback"
                                    disablePictureInPicture>
                                    <source src="stream_media.php?file=<?= urlencode($file['file_name']) ?>" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Watermark -->
        <div id="watermark" style="position:fixed; bottom:10px; right:10px;color:white; background:#0009; padding:5px 10px; z-index:10000; font-size:12px;">
            <?= $_SESSION['username'] ?> | <?= date('Y-m-d H:i') ?>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" style="max-height: 90vh;" alt="Full View Image" oncontextmenu="return false;">
                </div>
            </div>
        </div>
    </div>

    <!-- JS Includes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Disable right-click
        document.addEventListener("contextmenu", e => e.preventDefault());

        // Image modal logic
        const modalImage = document.getElementById("modalImage");
        const imageModal = document.getElementById("imageModal");
        imageModal.addEventListener("show.bs.modal", function(event) {
            const trigger = event.relatedTarget;
            const imageUrl = trigger.getAttribute("data-image");
            modalImage.src = imageUrl;
        });
    </script>
    <script src="../assets/js/protection.js"></script>

</body>

</html>