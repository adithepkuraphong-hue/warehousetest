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

$sql = "SELECT fp.id, fp.pr_no, fp.fp_product_id, fp.fp_product_name, fp.quantity, fp.source_machine,
               fp.warehouse, fp.row_location, fp.column_location, fp.level, fp.location_id,
               fp.received_at, fp.created_at
        FROM FDwarehouse fp
        INNER JOIN production_orders po ON po.pr_no = fp.pr_no
        WHERE po.status = 'เสร็จสิ้น'
          AND po.next_destination = 'FP Warehouse'
        ORDER BY fp.received_at DESC, fp.id DESC";
$result = $conn->query($sql);

$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

$summary_sql = "SELECT fp.fp_product_id, fp.fp_product_name, SUM(fp.quantity) AS total_quantity, COUNT(*) AS lots
                FROM FDwarehouse fp
                INNER JOIN production_orders po ON po.pr_no = fp.pr_no
                WHERE po.status = 'เสร็จสิ้น'
                  AND po.next_destination = 'FP Warehouse'
                GROUP BY fp.fp_product_id, fp.fp_product_name
                ORDER BY total_quantity DESC";
$summary_result = $conn->query($summary_sql);
$summary = array();
while ($row = $summary_result->fetch_assoc()) {
    $summary[] = $row;
}

jsonResponse(array('status' => 'success', 'data' => $rows, 'summary' => $summary));
?>
