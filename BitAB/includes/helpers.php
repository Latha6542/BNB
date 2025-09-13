<?php
// includes/helpers.php
require_once __DIR__.'/config.php';

function generate_tx_uuid() {
    // nicely readable UUID: prefix + random hex + timestamp
    return 'TX-'.bin2hex(random_bytes(6)).'-'.time();
}

function audit_log($entity_type, $entity_id, $action, $details = null, $performed_by = null) {
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO audit_logs (entity_type, entity_id, action, details, performed_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$entity_type, $entity_id, $action, $details, $performed_by]);
}
