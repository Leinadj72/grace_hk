<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

if (!$_SESSION['is_admin']) {
    header("Location: ../pages/dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$success = false;

// Handle adding new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_email'])) {
    $new_email = trim($_POST['new_email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$new_email]);
    $user = $stmt->fetch();

    if ($user) {
        if ($user['is_admin']) {
            $message = "⚠️ User is already an admin.";
        } else {
            $update = $pdo->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
            $update->execute([$user['id']]);
            $message = "✅ Admin added successfully.";
            $success = true;
        }
    } else {
        $message = "❌ No user found with that email.";
    }
}

// Handle admin removal with password verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_admin_id'], $_POST['admin_password'])) {
    $remove_id = intval($_POST['remove_admin_id']);
    $password = $_POST['admin_password'];

    if ($remove_id === $user_id) {
        $message = "⚠️ You cannot remove your own admin rights.";
    } else {
        // Get current admin's hashed password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $pdo->prepare("UPDATE users SET is_admin = 0 WHERE id = ?")->execute([$remove_id]);
            $message = "🗑️ Admin rights removed.";
            $success = true;
        } else {
            $message = "❌ Incorrect password.";
        }
    }
}

// Fetch all admins
$admins = $pdo->query("SELECT id, username, email FROM users WHERE is_admin = 1 ORDER BY username ASC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Admins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div>
                <a href="dashboard.php" class="btn btn-outline-light me-2">Dashboard</a>
                <a href="../pages/logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="mb-4">🛡️ Manage Admins</h3>

        <?php if ($message): ?>
            <div class="alert alert-<?= $success ? 'success' : 'danger' ?>"><?= $message ?></div>
        <?php endif; ?>

        <!-- Add Admin -->
        <form method="POST" class="mb-4">
            <div class="input-group">
                <input type="email" name="new_email" class="form-control" placeholder="Enter user email to make admin" required>
                <button type="submit" class="btn btn-primary">Add Admin</button>
            </div>
        </form>

        <!-- Admins Table -->
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $i => $admin): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                        <td>
                            <?php if ($admin['id'] !== $user_id): ?>
                                <!-- Password-Protected Remove Form -->
                                <form method="POST" class="d-flex gap-2 align-items-center" style="max-width: 300px;">
                                    <input type="hidden" name="remove_admin_id" value="<?= $admin['id'] ?>">
                                    <input type="password" name="admin_password" class="form-control form-control-sm" placeholder="Your password" required>
                                    <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>