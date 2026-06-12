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

$sql = "SELECT fp.id,
               fp.pr_no,
               fp.fg_product_id AS product_id,
               fp.fg_product_name AS product_name,
               fp.quantity,
               fp.source_machine,
               fp.warehouse,
               fp.row_location,
               fp.column_location,
               fp.level,
               fp.location_id,
               fp.received_at,
               fp.created_at
        FROM FGwarehouse fp
        INNER JOIN production_orders po ON po.pr_no = fp.pr_no
        WHERE po.status = 'เสร็จสิ้น'
          AND po.next_destination = 'FG Warehouse'
          AND fp.location_id IS NOT NULL
          AND fp.location_id <> ''
        ORDER BY fp.warehouse, fp.row_location, fp.column_location, fp.level, fp.received_at DESC";

$result = $conn->query($sql);
$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

jsonResponse(array('status' => 'success', 'data' => $rows));
?>
