<?php
// includes/api/public/login.php
session_start();

// Absolute path to config.php
require_once "C:/Users/latha/OneDrive/Desktop/BitAB/config/config.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // Prepare and execute MySQLi statement
    if ($stmt = $conn->prepare("SELECT id, password_hash, role, name FROM users WHERE email = ?")) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $u = $result->fetch_assoc();
        $stmt->close();

        if ($u && password_verify($pass, $u['password_hash'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['role'] = $u['role'];
            $_SESSION['name'] = $u['name'];
            header('Location: /admin_dashboard.php'); // Adjust if dashboard location is different
            exit;
        } else {
            $error = "Invalid credentials";
        }
    } else {
        $error = "Database error: " . $conn->error;
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container" style="max-width:420px">
    <h3>Sign in</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <input name="email" type="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input name="password" type="password" class="form-control" placeholder="Password" required>
        </div>
        <button class="btn btn-primary">Login</button>
    </form>
</div>
</body>
</html>
