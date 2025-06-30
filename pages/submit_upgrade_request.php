<?php
require '../includes/db.php';
require '../includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$transaction_id = trim($_POST['transaction_id'] ?? '');

if ($transaction_id === '') {
    header("Location: upgrade.php");
    exit();
}

// Check if user already submitted a request
$stmt = $pdo->prepare("SELECT id FROM upgrade_requests WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['upgrade_success'] = "You already submitted a request. Please wait for review.";
    header("Location: upgrade.php");
    exit();
}

// Insert new upgrade request
$stmt = $pdo->prepare("INSERT INTO upgrade_requests (user_id, transaction_id) VALUES (?, ?)");
$stmt->execute([$user_id, $transaction_id]);

$_SESSION['upgrade_success'] = "Your request has been submitted and is under review.";
header("Location: upgrade.php");
exit();
