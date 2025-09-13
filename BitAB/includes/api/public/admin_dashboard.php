<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin(); // stops execution if not admin

$user = current_user();
?>
<!doctype html>
<html>
<head>
  <link rel="stylesheet" href="style.css">
<meta charset="utf-8"><title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h2>Welcome, <?= htmlspecialchars($user['name']) ?></h2>
  <p><a class="btn btn-primary" href="/public/upload_budget.php">Upload Budget</a>
     <a class="btn btn-secondary" href="/public/create_transaction.php">Create Transaction</a>
     <a class="btn btn-danger" href="/public/logout.php">Logout</a></p>
</div>
</body>
</html>
