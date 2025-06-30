<?php
require '../includes/auth.php';
requireLogin();

$username = $_SESSION['username'];
$is_paid = $_SESSION['is_paid'];

if ($is_paid) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upgrade Account</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">My Hookup Site</a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-7 bg-white p-4 rounded shadow-sm">
                <h3 class="mb-4">Upgrade to Premium</h3>

                <p class="lead">Upgrade your account to unlock:</p>
                <ul>
                    <li>🔓 Access to full nude gallery & videos</li>
                    <li>📅 Priority booking access</li>
                    <li>💬 Private chat/contact with model</li>
                </ul>

                <hr>

                <h5>💰 Payment Instructions:</h5>
                <p>Send <strong>GHS 50</strong> to the number below and include your username as reference.</p>

                <div class="bg-light border rounded p-3 mb-3">
                    <strong>Mobile Money (Momo):</strong><br>
                    Name: <strong>Jane Doe</strong><br>
                    Number: <strong>055 123 4567</strong><br>
                    Network: <strong>MTN</strong>
                </div>

                <p>After payment, send your username and transaction ID to:</p>
                <ul>
                    <li>📱 WhatsApp: <a href="https://wa.me/233551234567" target="_blank">+233 55 123 4567</a></li>
                    <li>📧 Email: <a href="mailto:admin@hookupsite.com">admin@hookupsite.com</a></li>
                </ul>

                <div class="alert alert-warning mt-4">
                    ⚠️ Your account will be manually upgraded within 10-30 minutes after payment is confirmed.
                </div>

                <?php if (isset($_SESSION['upgrade_success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['upgrade_success'];
                                                        unset($_SESSION['upgrade_success']); ?></div>
                <?php endif; ?>

                <form method="POST" action="submit_upgrade_request.php" class="mt-4">
                    <h5>📤 Submit Payment Transaction ID</h5>
                    <div class="mb-3">
                        <input type="text" name="transaction_id" class="form-control" placeholder="Enter your Transaction ID" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit for Review</button>
                </form>

            </div>
        </div>
    </div>

</body>

</html>