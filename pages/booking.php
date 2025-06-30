<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$message = '';
$bookingSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $location = $_POST['location'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($date && $time && $contact) {
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_date, booking_time, location, contact, message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $date, $time, $location, $contact, $message]);
        $message = "✅ Booking request submitted!";
        $bookingSuccess = true;

        // Email Notification
        $to = "admin@yourdomain.com";
        $subject = "📅 New Booking Submitted";
        $email_body = "User: $username\nDate: $date\nTime: $time\nLocation: $location\nContact: $contact\nMessage: $message";
        @mail($to, $subject, $email_body);

        // WhatsApp Notification
        $wa_number = "233551234567";
        $wa_message = rawurlencode("📅 New Booking\nUser: $username\nDate: $date\nTime: $time\nLocation: $location\nContact: $contact\nMessage: $message");
        echo "<script>
            setTimeout(() => {
                window.open('https://wa.me/$wa_number?text=$wa_message', '_blank');
                window.location.href = 'booking.php?success=1';
            }, 1000);
        </script>";
    } else {
        $message = "❌ Please fill in all required fields (date, time, contact).";
    }
}

if (isset($_GET['success'])) {
    $message = "✅ Booking request submitted!";
    $bookingSuccess = true;
}

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date DESC, booking_time DESC");
$stmt->execute([$user_id]);
$myBookings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="../assets/images/icon.jpeg">
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
        <!-- Booking Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-md-6 bg-white p-4 rounded shadow-sm">
                <h3 class="mb-4 text-center">📅 Book a Hookup</h3>

                <?php if ($message): ?>
                    <div class="alert alert-<?= $bookingSuccess ? 'success' : 'danger' ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="time" class="form-label">Time</label>
                        <input type="time" name="time" id="time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="location" class="form-label">Preferred Location</label>
                        <input type="text" name="location" id="location" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="form-label">Your Contact (Phone/WhatsApp)</label>
                        <input type="text" name="contact" id="contact" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Additional Message</label>
                        <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit Booking</button>
                </form>
            </div>
        </div>

        <!-- My Bookings Table -->
        <div class="row justify-content-center">
            <div class="col-md-10 bg-white p-4 rounded shadow-sm">
                <h4 class="mb-3">🗂️ My Bookings</h4>
                <?php if (count($myBookings) === 0): ?>
                    <p class="text-muted">No bookings yet.</p>
                <?php else: ?>
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Contact</th>
                                <th>Message</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myBookings as $i => $b): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($b['booking_date']) ?></td>
                                    <td><?= htmlspecialchars($b['booking_time']) ?></td>
                                    <td><?= htmlspecialchars($b['location']) ?></td>
                                    <td><?= htmlspecialchars($b['contact']) ?></td>
                                    <td><?= htmlspecialchars($b['message']) ?></td>
                                    <td>
                                        <?php
                                        $status = $b['status'] ?? 'pending';
                                        $badge = match ($status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            default => 'warning'
                                        };
                                        echo "<span class='badge bg-$badge'>" . ucfirst($status) . "</span>";
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>