<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

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
        <script>document.addEventListener("contextmenu", e => e.preventDefault());</script>
        <script src="../assets/js/protection.js"></script>
    </body>
    </html>
    HTML;
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM media WHERE access = 'private' ORDER BY uploaded_at DESC");
$stmt->execute();
$media = $stmt->fetchAll();
$username = $_SESSION['username'];
$timestamp = date('Y-m-d H:i');
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
        .gallery-item-wrapper {
            position: relative;
        }

        .gallery-item {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }

        .media-watermark {
            position: absolute;
            bottom: 5px;
            right: 5px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            font-size: 12px;
            padding: 2px 6px;
            z-index: 2;
        }

        .long-press-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.85);
            color: white;
            display: none;
            justify-content: center;
            align-items: center;
            text-align: center;
            font-size: 1rem;
            padding: 1rem;
            z-index: 9999;
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
                        <div class="card shadow-sm gallery-item-wrapper">
                            <?php if ($file['file_type'] === 'image'): ?>
                                <div class="position-relative">
                                    <img src="stream_media.php?file=<?= urlencode($file['file_name']) ?>"
                                        class="gallery-item card-img-top"
                                        alt="Gallery Image"
                                        oncontextmenu="return false;"
                                        data-bs-toggle="modal"
                                        data-bs-target="#imageModal"
                                        data-image="stream_media.php?file=<?= urlencode($file['file_name']) ?>">
                                    <div class="media-watermark"><?= $username ?> | <?= $timestamp ?></div>
                                    <div class="long-press-overlay">⚠️ Screenshotting or long press is blocked.</div>
                                </div>
                            <?php elseif ($file['file_type'] === 'video'): ?>
                                <div class="position-relative">
                                    <video class="gallery-item card-img-top"
                                        muted controls
                                        oncontextmenu="return false;"
                                        controlsList="nodownload nofullscreen noremoteplayback"
                                        disablePictureInPicture>
                                        <source src="stream_media.php?file=<?= urlencode($file['file_name']) ?>" type="video/mp4">
                                    </video>
                                    <div class="media-watermark"><?= $username ?> | <?= $timestamp ?></div>
                                    <div class="long-press-overlay">⚠️ Screenshotting or long press is blocked.</div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal Full View -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-fullscreen">
            <div class="modal-content bg-dark border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center position-relative">
                    <img id="modalImage" src="" class="img-fluid" style="max-height: 90vh;" oncontextmenu="return false;">
                    <div class="media-watermark"><?= $username ?> | <?= $timestamp ?></div>
                    <div class="long-press-overlay">⚠️ Screenshotting or long press is blocked.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Includes -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("contextmenu", e => e.preventDefault());

        // Tap-hold (long press) block
        document.querySelectorAll('.gallery-item, #modalImage').forEach(el => {
            let pressTimer;
            const overlay = el.closest('.position-relative')?.querySelector('.long-press-overlay') ||
                document.querySelector('#imageModal .long-press-overlay');

            el.addEventListener("touchstart", () => {
                pressTimer = setTimeout(() => {
                    overlay.style.display = "flex";
                    setTimeout(() => overlay.style.display = "none", 2000);
                }, 500);
            });
            el.addEventListener("touchend", () => clearTimeout(pressTimer));
            el.addEventListener("touchmove", () => clearTimeout(pressTimer));
        });

        // Image modal logic
        const modalImage = document.getElementById("modalImage");
        document.getElementById("imageModal").addEventListener("show.bs.modal", function(event) {
            const trigger = event.relatedTarget;
            modalImage.src = trigger.getAttribute("data-image");
        });
    </script>
    <script src="../assets/js/protection.js"></script>

</body>

</html>