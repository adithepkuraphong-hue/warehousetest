<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../config.php';
require __DIR__ . '/app_helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = readJsonInput();
    $ok = logOrderHistory(
        $conn,
        $input['logType'] ?? 'Outbound',
        $input['action'] ?? 'Manual Log',
        $input['referenceType'] ?? null,
        $input['referenceId'] ?? null,
        $input['productId'] ?? null,
        $input['productName'] ?? null,
        intval($input['quantity'] ?? 0),
        $input['source'] ?? null,
        $input['destination'] ?? null,
        $input['status'] ?? null,
        $input['note'] ?? ''
    );

    if ($ok) {
        emitLiveUpdate('history.changed', array('action' => 'manual-log'));
    }
    jsonResponse(array('status' => $ok ? 'success' : 'error', 'message' => $ok ? 'Log saved' : 'Unable to save log'), $ok ? 201 : 500);
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(array('status' => 'error', 'message' => 'Method not allowed'), 405);
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$limit = isset($_GET['limit']) ? max(1, min(200, intval($_GET['limit']))) : 100;
$since_id = isset($_GET['since_id']) ? intval($_GET['since_id']) : 0;
$date_from = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';
$product = isset($_GET['product']) ? trim($_GET['product']) : '';
$action = isset($_GET['action']) ? trim($_GET['action']) : '';
$where = array();
$params = array();
$types = '';

if ($type === 'Inbound' || $type === 'Outbound') {
    $where[] = "log_type = ?";
    $params[] = $type;
    $types .= 's';
}

if ($since_id > 0) {
    $where[] = "id > ?";
    $params[] = $since_id;
    $types .= 'i';
}

if ($date_from !== '') {
    $where[] = "created_at >= ?";
    $params[] = $date_from . ' 00:00:00';
    $types .= 's';
}

if ($date_to !== '') {
    $where[] = "created_at <= ?";
    $params[] = $date_to . ' 23:59:59';
    $types .= 's';
}

if ($product !== '') {
    $where[] = "(product_name LIKE ? OR product_id LIKE ?)";
    $like = '%' . $product . '%';
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}

if ($action !== '') {
    $where[] = "action = ?";
    $params[] = $action;
    $types .= 's';
}

$sql = "SELECT * FROM OrderHis";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC LIMIT ?";
$params[] = $limit;
$types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

jsonResponse(array('status' => 'success', 'data' => $data));
?>
