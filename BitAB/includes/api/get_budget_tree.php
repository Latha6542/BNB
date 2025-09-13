<?php
// api/get_budget_tree.php
require_once __DIR__.'/../includes/config.php';

$institution_id = isset($_GET['institution_id']) ? (int)$_GET['institution_id'] : null;

$sql = "SELECT b.id as budget_id, b.title, b.amount, b.fiscal_year, d.id as dept_id, d.name as dept_name, p.id as project_id, p.name as project_name, p.allocated_amount
FROM budgets b
LEFT JOIN departments d ON d.institution_id = b.institution_id
LEFT JOIN projects p ON p.budget_id = b.id
";
$params = [];
if ($institution_id) {
    $sql .= " WHERE b.institution_id = ? ";
    $params[] = $institution_id;
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$tree = [];
foreach ($rows as $r) {
    $bid = $r['budget_id'];
    if (!isset($tree[$bid])) {
        $tree[$bid] = [
            'id'=>$bid,'title'=>$r['title'],'amount'=>$r['amount'],'fiscal_year'=>$r['fiscal_year'],'departments'=>[]
        ];
    }
    if ($r['dept_id']) {
        $did = $r['dept_id'];
        if (!isset($tree[$bid]['departments'][$did])) {
            $tree[$bid]['departments'][$did] = ['id'=>$did,'name'=>$r['dept_name'],'projects'=>[]];
        }
        if ($r['project_id']) {
            $pid = $r['project_id'];
            $tree[$bid]['departments'][$did]['projects'][$pid] = ['id'=>$pid,'name'=>$r['project_name'],'allocated_amount'=>$r['allocated_amount']];
        }
    }
}

jsonResponse(array_values($tree));
