<?php
// api/get_transactions.php
require_once __DIR__.'/../includes/config.php';

$budget_id = isset($_GET['budget_id']) ? (int)$_GET['budget_id'] : null;
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;

$sql = "SELECT t.*, u.name as created_by_name, v.name as vendor_name, p.name as project_name
FROM transactions t
LEFT JOIN users u ON u.id = t.created_by
LEFT JOIN vendors v ON v.id = t.vendor_id
LEFT JOIN projects p ON p.id = t.project_id
WHERE 1=1 ";

$params = [];
if ($budget_id) {
    $sql .= " AND t.budget_id = ? ";
    $params[] = $budget_id;
}
if ($project_id) {
    $sql .= " AND t.project_id = ? ";
    $params[] = $project_id;
}
$sql .= " ORDER BY t.created_at DESC LIMIT 500";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

jsonResponse($rows);
