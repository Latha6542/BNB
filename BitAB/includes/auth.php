<?php
// includes/auth.php
require_once __DIR__.'/config.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user() {
    global $pdo;
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT id,name,email,role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function require_admin() {
    if (!is_logged_in()) {
        header('Location: /public/login.php');
        exit;
    }
    $user = current_user();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo "Forbidden - admin only";
        exit;
    }
}
