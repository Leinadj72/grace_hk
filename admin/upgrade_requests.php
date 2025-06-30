<?php
require '../includes/db.php';
require '../includes/auth.php';
requireLogin();

if (!$_SESSION['is_admin']) {
    exit("Access denied.");
}

session_start(); // ensure session is active if not already

// Approve or reject logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['request_id'])) {
    $request_id = (int) $_POST['request_id'];
    $action = $_POST['action'];

    // Get user_id from request
    $stmt = $pdo->prepare("SELECT user_id FROM upgrade_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $req = $stmt->fetch();

    if ($req) {
        $user_id = $req['user_id'];

        if ($action === 'approve') {
            $pdo->prepare("UPDATE users SET is_paid = 1 WHERE id = ?")->execute([$user_id]);
            $pdo->prepare("UPDATE upgrade_requests SET status = 'approved' WHERE id = ?")->execute([$request_id]);
            $_SESSION['admin_message'] = "✅ Request #$request_id has been approved.";
        } elseif ($action === 'reject') {
            $pdo->prepare("UPDATE upgrade_requests SET status = 'rejected' WHERE id = ?")->execute([$request_id]);
            $_SESSION['admin_message'] = "❌ Request #$request_id has been rejected.";
        }
    }

    header("Location: upgrade_requests.php");
    exit();
}

// Fetch all requests
$stmt = $pdo->query("
    SELECT u.username, ur.id, ur.transaction_id, ur.status, ur.submitted_at 
    FROM upgrade_requests ur
    JOIN users u ON ur.user_id = u.id
    ORDER BY ur.submitted_at DESC
");
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Upgrade Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <h3>🔐 Admin Panel - Upgrade Requests</h3>

        <?php if (isset($_SESSION['admin_message'])): ?>
            <div class="alert alert-info"><?= $_SESSION['admin_message'];
                                            unset($_SESSION['admin_message']); ?></div>
        <?php endif; ?>

        <table class="table table-bordered table-striped mt-4">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Username</th>
                    <th>Transaction ID</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $i => $r): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($r['username']) ?></td>
                        <td><?= htmlspecialchars($r['transaction_id']) ?></td>
                        <td>
                            <?php
                            $badge = match ($r['status']) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                            };
                            echo "<span class='badge bg-$badge'>{$r['status']}</span>";
                            ?>
                        </td>
                        <td><?= $r['submitted_at'] ?></td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>

</html>