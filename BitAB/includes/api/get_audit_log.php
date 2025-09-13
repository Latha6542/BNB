<?php
// api/get_audit_log.php
require_once __DIR__.'/../includes/config.php';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200;
$stmt = $pdo->prepare("SELECT al.*, u.name as performed_by_name FROM audit_logs al LEFT JOIN users u ON u.id = al.performed_by ORDER BY al.performed_at DESC LIMIT ?");
$stmt->execute([$limit]);
jsonResponse($stmt->fetchAll());
