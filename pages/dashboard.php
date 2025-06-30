<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

if ($_SESSION['is_admin']) {
    header("Location: ../admin/dashboard.php");
    exit();
}

$username = $_SESSION['username'];
$is_paid = $_SESSION['is_paid'];

$success = $_SESSION['booking_success'] ?? '';
unset($_SESSION['booking_success']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/icon.jpeg">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My Hookup Site</a>
            <div class="d-flex">
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class="col-md-6 bg-white p-4 rounded shadow-sm">
                <h3 class="mb-3">Welcome, <?= htmlspecialchars($username) ?>!</h3>

                <p><strong>Account Type:</strong>
                    <span class="badge <?= $is_paid ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $is_paid ? 'Paid Member' : 'Free Member' ?>
                    </span>
                </p>

                <div class="mt-4">
                    <a href="public_gallery.php" class="btn btn-outline-primary w-100 mb-2">🌐 Public Gallery</a>
                    <?php if ($is_paid): ?>
                        <a href="gallery.php" class="btn btn-success w-100 mb-2">🔒 Private Gallery</a>
                    <?php endif; ?>
                    <a href="booking.php" class="btn btn-outline-dark w-100 mb-2">📅 Book a Hookup</a>
                    <?php if (!$is_paid): ?>
                        <a href="upgrade.php" class="btn btn-warning w-100">🚀 Upgrade Account</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>

</html>