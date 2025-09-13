<?php
// public/upload_budget.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
        echo "Upload failed";
        exit;
    }
    $csv = fopen($_FILES['csv']['tmp_name'],'r');
    // Expect header
    $header = fgetcsv($csv);
    $created = 0;
    while ($row = fgetcsv($csv)) {
        // Map fields robustly by header names if possible
        $data = array_combine($header, $row);
        $title = $data['title'] ?? $row[0];
        $amount = $data['amount'] ?? $row[1];
        $fy = $data['fiscal_year'] ?? ($row[2] ?? null);
        $inst_code = $data['institution_code'] ?? ($row[3] ?? null);

        // find institution
        if ($inst_code) {
            $stmt = $pdo->prepare("SELECT id FROM institutions WHERE code = ?");
            $stmt->execute([$inst_code]);
            $inst = $stmt->fetch();
            $inst_id = $inst ? $inst['id'] : null;
        } else {
            $inst_id = 1; // default if you want
        }
        if (!$inst_id) continue;

        $stmt = $pdo->prepare("INSERT INTO budgets (institution_id, title, amount, fiscal_year, uploaded_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$inst_id, $title, (float)$amount, $fy, $_SESSION['user_id']]);
        $bid = $pdo->lastInsertId();
        audit_log('budget', $bid, 'budget_uploaded', json_encode(['title'=>$title,'amount'=>$amount,'fy'=>$fy]), $_SESSION['user_id']);
        $created++;
    }
    fclose($csv);
    echo "Created $created budgets.";
    exit;
}

// Simple HTML form
?>
<!doctype html>
<html>
<head>
  <link rel="stylesheet" href="style.css">
  <title>Upload Budgets CSV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-3">
<div class="container">
  <h3>Upload Budget CSV</h3>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">CSV File (title,amount,fiscal_year,institution_code)</label>
      <input type="file" name="csv" accept=".csv" class="form-control" required>
    </div>
    <button class="btn btn-primary">Upload</button>
  </form>
</div>
</body>
</html>
