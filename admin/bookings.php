<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin();

if (!$_SESSION['is_admin']) {
    exit("Access denied.");
}

// Optional: Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['booking_id']]);
    header("Location: bookings.php");
    exit();
}

// Fetch bookings with user info
$stmt = $pdo->query("
    SELECT b.*, u.username 
    FROM bookings b 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC
");
$bookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h3>📅 Booking Requests</h3>

        <?php if (empty($bookings)): ?>
            <div class="alert alert-info mt-4">No bookings found.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped mt-4">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Date</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $i => $b): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($b['username']) ?></td>
                            <td><?= htmlspecialchars($b['contact']) ?></td>
                            <td><?= htmlspecialchars($b['booking_date']) ?></td>
                            <td><?= nl2br(htmlspecialchars($b['message'])) ?></td>
                            <td>
                                <?php
                                $badge = match ($b['status']) {
                                    'pending' => 'warning',
                                    'completed' => 'success',
                                    'canceled' => 'danger',
                                    default => 'secondary',
                                };
                                echo "<span class='badge bg-$badge'>{$b['status']}</span>";
                                ?>
                            </td>
                            <td><?= $b['created_at'] ?></td>
                            <td>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="pending" <?= $b['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="completed" <?= $b['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="canceled" <?= $b['status'] === 'canceled' ? 'selected' : '' ?>>Canceled</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>

</html>