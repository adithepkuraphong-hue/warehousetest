<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../config.php';
require __DIR__ . '/app_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$input = readJsonInput();

try {
    if ($method === 'GET') {
        getProductionOrders($conn);
    }

    if ($method === 'POST') {
        createProductionOrder($conn, $input);
    }

    if ($method === 'PUT') {
        updateProductionOrder($conn, $input);
    }

    jsonResponse(array('status' => 'error', 'message' => 'Method not allowed'), 405);
} catch (Exception $e) {
    jsonResponse(array('status' => 'error', 'message' => $e->getMessage()), 500);
}

function getProductionOrders($conn) {
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $machine = isset($_GET['machine']) ? $_GET['machine'] : '';
    $where = array();
    $params = array();
    $types = '';

    if ($status !== '') {
        $where[] = "status = ?";
        $params[] = $status;
        $types .= 's';
    }

    if ($machine !== '') {
        $where[] = "machine_type = ?";
        $params[] = $machine;
        $types .= 's';
    }

    $sql = "SELECT * FROM production_orders";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $sql .= " ORDER BY FIELD(status, 'กำลังผลิต', 'รอผลิต', 'เสร็จสิ้น'), created_at DESC";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = array();
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    jsonResponse(array('status' => 'success', 'data' => $data));
}

function createProductionOrder($conn, $input) {
    $required = array('sourceProductId', 'sourceProductName', 'quantity', 'machineType');
    foreach ($required as $field) {
        if (!isset($input[$field]) || $input[$field] === '') {
            jsonResponse(array('status' => 'error', 'message' => "Missing field: $field"), 400);
        }
    }

    $machine = $input['machineType'];
    if ($machine !== 'Printer' && $machine !== 'Cutter') {
        jsonResponse(array('status' => 'error', 'message' => 'machineType must be Printer or Cutter'), 400);
    }

    $quantity = intval($input['quantity']);
    if ($quantity <= 0) {
        jsonResponse(array('status' => 'error', 'message' => 'Quantity must be greater than 0'), 400);
    }

    $pr_no = generatePrNo($conn);
    $source_inventory_id = isset($input['sourceInventoryId']) ? intval($input['sourceInventoryId']) : null;
    $source_product_id = $input['sourceProductId'];
    $source_product_name = $input['sourceProductName'];
    $final_product_id = isset($input['finalProductId']) && $input['finalProductId'] !== '' ? $input['finalProductId'] : 'FP-' . $source_product_id;
    $final_product_name = isset($input['finalProductName']) && $input['finalProductName'] !== '' ? $input['finalProductName'] : 'FP ' . $source_product_name;
    $parent_pr_id = isset($input['parentPrId']) ? intval($input['parentPrId']) : null;

    $sql = "INSERT INTO production_orders (pr_no, source_inventory_id, source_product_id, source_product_name, final_product_id, final_product_name, quantity, machine_type, parent_pr_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssisi", $pr_no, $source_inventory_id, $source_product_id, $source_product_name, $final_product_id, $final_product_name, $quantity, $machine, $parent_pr_id);

    if (!$stmt->execute()) {
        jsonResponse(array('status' => 'error', 'message' => $stmt->error), 500);
    }

    $new_id = $conn->insert_id;
    logOrderHistory($conn, 'Outbound', 'Create Production Order', 'PR', $pr_no, $source_product_id, $source_product_name, $quantity, 'Raw Material Warehouse', $machine, 'รอผลิต', 'Created from dispatch');

    jsonResponse(array('status' => 'success', 'message' => 'Production order created', 'id' => $new_id, 'pr_no' => $pr_no), 201);
}

function updateProductionOrder($conn, $input) {
    if (empty($input['id']) || empty($input['action'])) {
        jsonResponse(array('status' => 'error', 'message' => 'id and action are required'), 400);
    }

    $id = intval($input['id']);
    $action = $input['action'];
    $order = findProductionOrder($conn, $id);
    if (!$order) {
        jsonResponse(array('status' => 'error', 'message' => 'Production order not found'), 404);
    }

    if ($action === 'claim') {
        claimOrder($conn, $order);
    }

    if ($action === 'complete') {
        completeOrder($conn, $order, $input);
    }

    jsonResponse(array('status' => 'error', 'message' => 'Unknown action'), 400);
}

function claimOrder($conn, $order) {
    if ($order['status'] !== 'รอผลิต') {
        jsonResponse(array('status' => 'error', 'message' => 'Only pending orders can be claimed'), 400);
    }

    $sql = "UPDATE production_orders SET status = 'กำลังผลิต', claimed_at = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $id = intval($order['id']);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    logOrderHistory($conn, 'Outbound', 'Claim Production Order', 'PR', $order['pr_no'], $order['source_product_id'], $order['source_product_name'], $order['quantity'], $order['machine_type'], $order['machine_type'], 'กำลังผลิต', '');
    jsonResponse(array('status' => 'success', 'message' => 'Order claimed'));
}

function completeOrder($conn, $order, $input) {
    if ($order['status'] !== 'กำลังผลิต') {
        jsonResponse(array('status' => 'error', 'message' => 'Only in-progress orders can be completed'), 400);
    }

    if (empty($input['destination'])) {
        jsonResponse(array('status' => 'error', 'message' => 'destination is required'), 400);
    }

    $destination = $input['destination'];
    if (!in_array($destination, array('FP Warehouse', 'Printer', 'Cutter'))) {
        jsonResponse(array('status' => 'error', 'message' => 'Invalid destination'), 400);
    }
    if ($destination === $order['machine_type']) {
        jsonResponse(array('status' => 'error', 'message' => 'Destination machine must be different from current machine'), 400);
    }

    $conn->begin_transaction();

    try {
        $id = intval($order['id']);
        $sql = "UPDATE production_orders SET status = 'เสร็จสิ้น', next_destination = ?, completed_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $destination, $id);
        $stmt->execute();

        if ($destination === 'FP Warehouse') {
            $sql_fp = "INSERT INTO FDwarehouse (pr_no, fp_product_id, fp_product_name, quantity, source_machine) VALUES (?, ?, ?, ?, ?)";
            $stmt_fp = $conn->prepare($sql_fp);
            $qty = intval($order['quantity']);
            $stmt_fp->bind_param("sssis", $order['pr_no'], $order['final_product_id'], $order['final_product_name'], $qty, $order['machine_type']);
            $stmt_fp->execute();

            logOrderHistory($conn, 'Inbound', 'Receive Final Product', 'PR', $order['pr_no'], $order['final_product_id'], $order['final_product_name'], $order['quantity'], $order['machine_type'], 'FP Warehouse', 'Received', '');
        } else {
            $next_input = array(
                'sourceProductId' => $order['final_product_id'],
                'sourceProductName' => $order['final_product_name'],
                'finalProductId' => $order['final_product_id'],
                'finalProductName' => $order['final_product_name'],
                'quantity' => $order['quantity'],
                'machineType' => $destination,
                'parentPrId' => $id
            );
            $next_pr_no = generatePrNo($conn);
            $parent_pr_id = $id;
            $source_inventory_id = null;
            $qty = intval($next_input['quantity']);
            $sql_next = "INSERT INTO production_orders (pr_no, source_inventory_id, source_product_id, source_product_name, final_product_id, final_product_name, quantity, machine_type, parent_pr_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_next = $conn->prepare($sql_next);
            $stmt_next->bind_param("sissssisi", $next_pr_no, $source_inventory_id, $next_input['sourceProductId'], $next_input['sourceProductName'], $next_input['finalProductId'], $next_input['finalProductName'], $qty, $destination, $parent_pr_id);
            $stmt_next->execute();

            logOrderHistory($conn, 'Outbound', 'Transfer To Next Machine', 'PR', $order['pr_no'], $order['final_product_id'], $order['final_product_name'], $order['quantity'], $order['machine_type'], $destination, 'รอผลิต', 'Next PR: ' . $next_pr_no);
        }

        logOrderHistory($conn, 'Outbound', 'Complete Production Order', 'PR', $order['pr_no'], $order['source_product_id'], $order['source_product_name'], $order['quantity'], $order['machine_type'], $destination, 'เสร็จสิ้น', '');
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(array('status' => 'error', 'message' => $e->getMessage()), 500);
    }

    jsonResponse(array('status' => 'success', 'message' => 'Order completed'));
}

function findProductionOrder($conn, $id) {
    $sql = "SELECT * FROM production_orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows ? $result->fetch_assoc() : null;
}

function generatePrNo($conn) {
    do {
        $pr_no = 'PR' . date('ymdHis') . random_int(10, 99);
        $stmt = $conn->prepare("SELECT id FROM production_orders WHERE pr_no = ?");
        $stmt->bind_param("s", $pr_no);
        $stmt->execute();
        $exists = $stmt->get_result()->num_rows > 0;
    } while ($exists);

    return $pr_no;
}
?>
