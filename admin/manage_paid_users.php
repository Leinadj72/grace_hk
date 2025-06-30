<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin();

if (!$_SESSION['is_admin']) {
    exit("Access denied.");
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) $_POST['user_id'];

    // Prevent editing admins
    $check = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
    $check->execute([$userId]);
    $user = $check->fetch();

    if ($user && !$user['is_admin']) {
        if (isset($_POST['make_paid'])) {
            $stmt = $pdo->prepare("UPDATE users SET is_paid = 1, paid_until = DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id = ?");
            $stmt->execute([$userId]);
        } elseif (isset($_POST['remove_paid'])) {
            $stmt = $pdo->prepare("UPDATE users SET is_paid = 0, paid_until = NULL WHERE id = ?");
            $stmt->execute([$userId]);
        }
    }
}

// Fetch all users
$users = $pdo->query("SELECT id, username, email, is_paid, paid_until, is_admin FROM users ORDER BY username ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Paid Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/icon.jpeg">
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

    <div class="container py-5">
        <h2 class="mb-4 text-center">Manage Paid Users</h2>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Paid Until</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge bg-primary">Admin (Always Paid)</span>
                                <?php elseif ($user['is_paid']): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Free</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $user['is_admin'] ? 'N/A' : ($user['paid_until'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!$user['is_admin']): ?>
                                    <form method="post" class="d-flex gap-2">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <?php if (!$user['is_paid']): ?>
                                            <button type="submit" name="make_paid" class="btn btn-success btn-sm">Add 30 Days</button>
                                        <?php else: ?>
                                            <button type="submit" name="remove_paid" class="btn btn-danger btn-sm">Remove Access</button>
                                        <?php endif; ?>
                                    </form>
                                <?php else: ?>
                                    <em class="text-muted">No action</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (count($users) === 0): ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>