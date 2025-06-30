<?php
session_start();
require_once 'db.php';

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

    global $pdo;

    // Fetch latest user data
    $stmt = $pdo->prepare("SELECT id, username, is_paid, is_admin, paid_until FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // Refresh session data
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = (bool)$user['is_admin'];

    // Only apply paid check for non-admin users
    if (!$user['is_admin']) {
        if (!empty($user['paid_until']) && strtotime($user['paid_until']) < time()) {
            // Expired – remove paid status
            $pdo->prepare("UPDATE users SET is_paid = 0, paid_until = NULL WHERE id = ?")->execute([$user['id']]);
            $_SESSION['is_paid'] = false;
        } else {
            $_SESSION['is_paid'] = (bool)$user['is_paid'];
        }
    } else {
        $_SESSION['is_paid'] = true; // Admins always treated as paid
    }
}
