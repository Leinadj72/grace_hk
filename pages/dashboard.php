<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

$username = $_SESSION['username'];
$is_paid = $_SESSION['is_paid'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <div class="row justify-content-center">
            <div class="col-md-6 bg-white p-4 rounded shadow-sm">
                <h3 class="mb-3">Welcome, <?= htmlspecialchars($username) ?>!</h3>

                <p><strong>Account Type:</strong>
                    <span class="badge <?= $is_paid ? 'bg-success' : 'bg-secondary' ?>">
                        <?= $is_paid ? 'Paid Member' : 'Free Member' ?>
                    </span>
                </p>

                <?php if (!$is_paid): ?>
                    <div class="alert alert-warning mt-4">
                        You are currently on a free account. Upgrade to access private content!
                        <br><br>
                        <a href="upgrade.php" class="btn btn-warning mt-2">Upgrade Account</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success mt-4">
                        🎉 Thank you for being a paid member! You now have full access.
                        <br><br>
                        <a href="gallery.php" class="btn btn-success mt-2">Go to Private Gallery</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>