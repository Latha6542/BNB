<?php
// public/create_transaction.php
require_once __DIR__.'/../includes/config.php';
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/helpers.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $budget_id = (int)$_POST['budget_id'];
    $project_id = (!empty($_POST['project_id'])) ? (int)$_POST['project_id'] : null;
    $vendor_id = (!empty($_POST['vendor_id'])) ? (int)$_POST['vendor_id'] : null;
    $amount = (float)$_POST['amount'];
    $reference = $_POST['reference'] ?? null;
    $metadata = $_POST['metadata'] ?? null;

    $tx_uuid = generate_tx_uuid();
    $stmt = $pdo->prepare("INSERT INTO transactions (tx_uuid, budget_id, project_id, vendor_id, amount, status, reference, created_by, metadata) VALUES (?, ?, ?, ?, ?, 'initiated', ?, ?, ?)");
    $stmt->execute([$tx_uuid, $budget_id, $project_id, $vendor_id, $amount, $reference, $_SESSION['user_id'], $metadata ? json_encode($metadata) : null]);
    $tx_id = $pdo->lastInsertId();
    audit_log('transaction', $tx_id, 'created', json_encode(['tx_uuid'=>$tx_uuid,'amount'=>$amount,'project_id'=>$project_id]), $_SESSION['user_id']);
    echo "Transaction created: $tx_uuid";
    exit;
}

// Simple form for admin
$stmt = $pdo->query("SELECT id,title FROM budgets ORDER BY id DESC");
$budgets = $stmt->fetchAll();
$stmt = $pdo->query("SELECT id,name FROM projects ORDER BY id DESC");
$projects = $stmt->fetchAll();
$stmt = $pdo->query("SELECT id,name FROM vendors ORDER BY id DESC");
$vendors = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
  <link rel="stylesheet" href="style.css">
  <title>Create Transaction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container">
  <h3>Create Transaction</h3>
  <form method="post">
    <div class="mb-3">
      <label>Budget</label>
      <select name="budget_id" class="form-select" required>
        <?php foreach($budgets as $b): ?>
          <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Project (optional)</label>
      <select name="project_id" class="form-select">
        <option value="">--none--</option>
        <?php foreach($projects as $p): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Vendor (optional)</label>
      <select name="vendor_id" class="form-select">
        <option value="">--none--</option>
        <?php foreach($vendors as $v): ?>
          <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label>Amount</label>
      <input type="number" step="0.01" name="amount" class="form-control" required>
    </div>
    <div class="mb-3">
      <label>Reference</label>
      <input type="text" name="reference" class="form-control">
    </div>
    <button class="btn btn-success">Create</button>
  </form>
</div>
</body>
</html>
