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

$method = $_SERVER['REQUEST_METHOD'];
$input = readJsonInput();

try {
    if ($method === 'GET') {
        $action = isset($_GET['action']) ? $_GET['action'] : 'materials';
        if ($action === 'materials') {
            getMaterials($conn);
        }
        if ($action === 'locations') {
            getMaterialLocations($conn);
        }
        if ($action === 'rid') {
            previewRid($conn);
        }
        jsonResponse(array('status' => 'error', 'message' => 'Unknown action'), 400);
    }

    if ($method === 'POST') {
        createProductOrder($conn, $input);
    }

    jsonResponse(array('status' => 'error', 'message' => 'Method not allowed'), 405);
} catch (Exception $e) {
    jsonResponse(array('status' => 'error', 'message' => $e->getMessage()), 500);
}

function getMaterials($conn) {
    $sql = "SELECT product_name, SUM(quantity) AS total_quantity, COUNT(*) AS lots
            FROM inventory
            GROUP BY product_name
            ORDER BY product_name ASC";
    $result = $conn->query($sql);
    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $row['total_quantity'] = intval($row['total_quantity']);
        $row['lots'] = intval($row['lots']);
        $row['disabled'] = $row['total_quantity'] <= 0;
        $rows[] = $row;
    }

    jsonResponse(array('status' => 'success', 'data' => $rows));
}

function getMaterialLocations($conn) {
    $material = isset($_GET['material']) ? trim($_GET['material']) : '';
    if ($material === '') {
        jsonResponse(array('status' => 'error', 'message' => 'material is required'), 400);
    }

    $sql = "SELECT id, product_id, product_name, quantity, warehouse, row_location, column_location, level, location_id
            FROM inventory
            WHERE product_name = ? AND quantity > 0
            ORDER BY warehouse, row_location, column_location, level, location_id";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $material);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = array();
    while ($row = $result->fetch_assoc()) {
        $row['quantity'] = intval($row['quantity']);
        $rows[] = $row;
    }

    jsonResponse(array('status' => 'success', 'data' => $rows));
}

function previewRid($conn) {
    $order_date = isset($_GET['date']) ? trim($_GET['date']) : '';
    if (!isValidDate($order_date)) {
        jsonResponse(array('status' => 'error', 'message' => 'Valid date is required'), 400);
    }

    jsonResponse(array('status' => 'success', 'rid' => generateRid($conn, $order_date)));
}

function createProductOrder($conn, $input) {
    $product_name = isset($input['productName']) ? trim($input['productName']) : '';
    $material_name = isset($input['materialName']) ? trim($input['materialName']) : '';
    $inventory_id = isset($input['materialInventoryId']) ? intval($input['materialInventoryId']) : 0;
    $location_id = isset($input['locationId']) ? trim($input['locationId']) : '';
    $quantity = isset($input['quantity']) ? intval($input['quantity']) : 0;
    $order_date = isset($input['orderDate']) ? trim($input['orderDate']) : '';
    $order_time = isset($input['orderTime']) ? trim($input['orderTime']) : '';

    if (!in_array($product_name, array('กล่อง A', 'ลัง B', 'พาเลต C'), true)) {
        jsonResponse(array('status' => 'error', 'message' => 'Invalid product name'), 400);
    }
    if ($material_name === '' || $inventory_id <= 0 || $location_id === '') {
        jsonResponse(array('status' => 'error', 'message' => 'Material and LID are required'), 400);
    }
    if ($quantity <= 0) {
        jsonResponse(array('status' => 'error', 'message' => 'Quantity must be greater than 0'), 400);
    }
    if (!isValidDate($order_date) || !preg_match('/^\d{2}:\d{2}$/', $order_time)) {
        jsonResponse(array('status' => 'error', 'message' => 'Valid date and time are required'), 400);
    }

    $conn->begin_transaction();

    try {
        $stock_sql = "SELECT id, product_id, product_name, quantity, location_id, warehouse, row_location, column_location, level
                      FROM inventory
                      WHERE id = ? AND product_name = ? AND location_id = ?
                      FOR UPDATE";
        $stock_stmt = $conn->prepare($stock_sql);
        $stock_stmt->bind_param("iss", $inventory_id, $material_name, $location_id);
        $stock_stmt->execute();
        $stock = $stock_stmt->get_result()->fetch_assoc();

        if (!$stock) {
            throw new Exception('Selected material/LID was not found');
        }
        if (intval($stock['quantity']) < $quantity) {
            throw new Exception('Not enough material in selected LID');
        }

        $remaining = intval($stock['quantity']) - $quantity;
        $new_status = $remaining <= 0 ? 'Out Stock' : 'In Stock';
        $update_sql = "UPDATE inventory SET quantity = ?, status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isi", $remaining, $new_status, $inventory_id);
        $update_stmt->execute();

        $rid = generateRid($conn, $order_date);
        $machine = 'Printer';
        $fg_product_id = 'FG-' . $rid;

        $prod_sql = "INSERT INTO production_orders (pr_no, source_inventory_id, source_product_id, source_product_name, fg_product_id, fg_product_name, quantity, machine_type)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $prod_stmt = $conn->prepare($prod_sql);
        $prod_stmt->bind_param("sissssis", $rid, $inventory_id, $stock['product_id'], $material_name, $fg_product_id, $product_name, $quantity, $machine);
        $prod_stmt->execute();
        $production_order_id = $conn->insert_id;

        $request_sql = "INSERT INTO product_order_requests (rid, product_name, material_inventory_id, material_product_id, material_name, quantity, location_id, order_date, order_time, production_order_id, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'รอผลิต')";
        $request_stmt = $conn->prepare($request_sql);
        $request_stmt->bind_param("ssississsi", $rid, $product_name, $inventory_id, $stock['product_id'], $material_name, $quantity, $location_id, $order_date, $order_time, $production_order_id);
        $request_stmt->execute();

        logOrderHistory($conn, 'Outbound', 'Open Product Order', 'RID', $rid, $stock['product_id'], $material_name, $quantity, $location_id, 'Production Queue', 'รอผลิต', 'Product: ' . $product_name);
        $conn->commit();

        emitLiveUpdate('inventory.changed', array('action' => 'dispatch', 'id' => $inventory_id));
        emitLiveUpdate('production.changed', array('action' => 'create', 'id' => $production_order_id, 'rid' => $rid));
        emitLiveUpdate('history.changed', array('action' => 'open-product-order', 'rid' => $rid));

        jsonResponse(array(
            'status' => 'success',
            'message' => 'Product order created',
            'rid' => $rid,
            'productionOrderId' => $production_order_id,
            'remainingQuantity' => $remaining
        ), 201);
    } catch (Exception $e) {
        $conn->rollback();
        jsonResponse(array('status' => 'error', 'message' => $e->getMessage()), 400);
    }
}

function generateRid($conn, $order_date) {
    $prefix = 'PD' . str_replace('-', '', $order_date);
    $like = $prefix . '%';

    $sql = "SELECT MAX(CAST(SUBSTRING(rid, 11) AS UNSIGNED)) AS max_seq
            FROM product_order_requests
            WHERE rid LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $next = intval($row['max_seq'] ?? 0) + 1;

    return $prefix . str_pad(strval($next), 3, '0', STR_PAD_LEFT);
}

function isValidDate($date) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        return false;
    }
    $parts = explode('-', $date);
    return checkdate(intval($parts[1]), intval($parts[2]), intval($parts[0]));
}
?>
