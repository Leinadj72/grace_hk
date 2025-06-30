<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $time = $_POST['time'] ?? '';
    $location = $_POST['location'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if ($date && $time) {
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, booking_date, booking_time, location, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $date, $time, $location, $notes]);
        $message = "✅ Booking request submitted!";
    } else {
        $message = "❌ Please select date and time.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
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
            <div class="col-md-6 bg-white p-4 rounded shadow-sm">
                <h3 class="mb-4 text-center">Book a Hookup</h3>

                <?php if ($message): ?>
                    <div class="alert alert-info"><?= $message ?></div>
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
                        <input type="text" name="location" id="location" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit Booking</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>