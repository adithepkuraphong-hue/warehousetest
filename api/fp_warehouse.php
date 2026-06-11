<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../config.php';
require __DIR__ . '/app_helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(array('status' => 'error', 'message' => 'Method not allowed'), 405);
}

$sql = "SELECT id, pr_no, fp_product_id, fp_product_name, quantity, source_machine, received_at, created_at
        FROM FDwarehouse
        ORDER BY received_at DESC, id DESC";
$result = $conn->query($sql);

$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$summary_sql = "SELECT fp_product_id, fp_product_name, SUM(quantity) AS total_quantity, COUNT(*) AS lots
                FROM FDwarehouse
                GROUP BY fp_product_id, fp_product_name
                ORDER BY total_quantity DESC";
$summary_result = $conn->query($summary_sql);
$summary = array();
while ($row = $summary_result->fetch_assoc()) {
    $summary[] = $row;
}

jsonResponse(array('status' => 'success', 'data' => $rows, 'summary' => $summary));
?>
