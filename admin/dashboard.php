<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin();

// Ensure only admins can access
if (!$_SESSION['is_admin']) {
    exit("Access denied.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/icon.jpeg">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="d-flex">
                <a href="../pages/logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>

        <div class="row g-4">

            <div class="col-md-4">
                <a href="upgrade_requests.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">🔐 Upgrade Requests</h5>
                            <p class="card-text">Approve or reject user upgrade requests.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="media_upload.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">🖼️ Manage Media</h5>
                            <p class="card-text">Upload images and videos to the gallery.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="bookings.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">📅 View Bookings</h5>
                            <p class="card-text">See all user bookings.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="../admin/manage_admin.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">⚙️ Manage Admins</h5>
                            <p class="card-text">Promote or remove admin roles.</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>
    </div>

</body>

</html>