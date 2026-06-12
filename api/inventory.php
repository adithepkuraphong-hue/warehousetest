<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require '../config.php';
require __DIR__ . '/app_helpers.php';

// Read input once and store globally
$raw_input = file_get_contents("php://input");
$GLOBALS['input_data'] = null;

// Try to parse JSON input
if (!empty($raw_input)) {
    $GLOBALS['input_data'] = json_decode($raw_input, true);
    // If JSON decode failed, it's still null
}

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($request_uri, '/'));
$endpoint = pathinfo(end($path_parts), PATHINFO_FILENAME); // Remove .php extension

$response = array();

try {
    // Handle both GET/POST (for list/create) and GET/PUT/DELETE with query params (for single item)
    if ($request_method == 'GET' && isset($_GET['id'])) {
        $response = getById();
    } elseif ($request_method == 'GET') {
        $response = getAll();
    } elseif ($request_method == 'POST') {
        $response = create();
    } elseif ($request_method == 'PUT') {
        $response = update();
    } elseif ($request_method == 'DELETE') {
        $response = delete();
    } else {
        http_response_code(405);
        $response = array('status' => 'error', 'message' => 'Method not allowed');
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = array('status' => 'error', 'message' => $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Helper function to get input data from various sources
function getInput() {
    // Use global input data that was parsed once at the beginning
    if ($GLOBALS['input_data'] !== null) {
        return $GLOBALS['input_data'];
    }
    
    // Fallback to $_POST
    if (!empty($_POST)) {
        return $_POST;
    }
    
    // Fallback to $_GET for read-only operations
    if (!empty($_GET)) {
        return $_GET;
    }
    
    return null;
}

// Helper function to convert camelCase to snake_case
function convertInputFields($input) {
    if (!$input) return $input;
    
    // Convert camelCase to snake_case
    $mappings = [
        'productId' => 'product_id',
        'productName' => 'product_name',
        'warehouseCode' => 'warehouse',
        'rowLocation' => 'row_location',
        'columnLocation' => 'column_location'
    ];
    
    foreach ($mappings as $camel => $snake) {
        if (isset($input[$camel])) {
            $input[$snake] = $input[$camel];
        }
    }
    
    return $input;
}

// Helper function to generate location ID: Warehouse + Zone - Row - Level.
function generateLocationId($warehouse, $zone, $row, $level) {
    return strtoupper($warehouse) . strtoupper($zone) . '-' . $row . '-' . $level;
}

function normalizeInventoryStatus($quantity, $status = null) {
    if (intval($quantity) <= 0) {
        return 'Out Stock';
    }

    if ($status === 'Empty' || $status === 'Out Stock') {
        return 'Out Stock';
    }

    return 'In Stock';
}

function isAllowedProductName($product_name) {
    return in_array($product_name, array('Paper', 'Wood', 'Plastic'), true);
}

function validateLocation($warehouse, $zone, $row, $level) {
    if (!preg_match('/^[A-B]$/', $warehouse)) {
        http_response_code(400);
        return 'Warehouse must be A or B';
    }
    if (!preg_match('/^[A-C]$/', $zone)) {
        http_response_code(400);
        return 'Zone must be A, B, or C';
    }
    if ($row < 1 || $row > 5) {
        http_response_code(400);
        return 'Row must be between 1 and 5';
    }
    if ($level < 0 || $level > 3) {
        http_response_code(400);
        return 'Level must be between 0 and 3';
    }

    return null;
}

// GET all items
function getAll() {
    global $conn;
    
    $sql = "SELECT id, product_id, product_name, quantity,
            CASE
                WHEN quantity <= 0 THEN 'Out Stock'
                WHEN status IN ('Empty', 'Out Stock') THEN 'Out Stock'
                ELSE 'In Stock'
            END AS status,
            warehouse, row_location, column_location, level, location_id, created_at, updated_at
            FROM inventory ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return array('status' => 'success', 'data' => $data);
    } else {
        return array('status' => 'success', 'data' => array());
    }
}

// GET by ID
function getById() {
    global $conn;
    
    $input = getInput();
    $id = isset($_GET['id']) ? $_GET['id'] : (isset($input['id']) ? $input['id'] : null);
    
    if (!$id) {
        http_response_code(400);
        return array('status' => 'error', 'message' => 'ID is required');
    }
    
    $sql = "SELECT id, product_id, product_name, quantity,
            CASE
                WHEN quantity <= 0 THEN 'Out Stock'
                WHEN status IN ('Empty', 'Out Stock') THEN 'Out Stock'
                ELSE 'In Stock'
            END AS status,
            warehouse, row_location, column_location, level, location_id, created_at, updated_at
            FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return array('status' => 'success', 'data' => $result->fetch_assoc());
    } else {
        http_response_code(404);
        return array('status' => 'error', 'message' => 'Item not found');
    }
}

// CREATE new item
function create() {
    global $conn;
    
    $input = getInput();
    $input = convertInputFields($input);
    
    // Validate required fields
    if (!$input || empty($input['product_id']) || empty($input['product_name']) || 
        !isset($input['quantity'])) {
        http_response_code(400);
        return array(
            'status' => 'error', 
            'message' => 'Missing required fields: product_id, product_name, quantity'
        );
    }
    
    $product_id = $input['product_id'];
    $product_name = $input['product_name'];
    $quantity = intval($input['quantity']);
    $status = normalizeInventoryStatus($quantity, isset($input['status']) ? $input['status'] : null);

    if (!isAllowedProductName($product_name)) {
        http_response_code(400);
        return array('status' => 'error', 'message' => 'Product Name must be Paper, Wood, or Plastic');
    }
    
    // Location fields (optional)
    $warehouse = isset($input['warehouse']) ? strtoupper($input['warehouse']) : 'A';
    $zone = isset($input['row_location']) ? strtoupper($input['row_location']) : 'A';
    $row = isset($input['column_location']) ? intval($input['column_location']) : 1;
    $level = isset($input['level']) ? intval($input['level']) : 0;
    $location_id = generateLocationId($warehouse, $zone, $row, $level);
    
    $location_error = validateLocation($warehouse, $zone, $row, $level);
    if ($location_error) {
        return array('status' => 'error', 'message' => $location_error);
    }
    
    $sql = "INSERT INTO inventory (product_id, product_name, quantity, status, warehouse, row_location, column_location, level, location_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssiis", $product_id, $product_name, $quantity, $status, $warehouse, $zone, $row, $level, $location_id);
    
    if ($stmt->execute()) {
        http_response_code(201);
        logOrderHistory($conn, 'Inbound', 'Create Inventory Item', 'Inventory', strval($conn->insert_id), $product_id, $product_name, $quantity, 'Manual Input', $location_id, $status, '');
        emitLiveUpdate('inventory.changed', array('action' => 'create', 'id' => $conn->insert_id));
        emitLiveUpdate('history.changed', array('action' => 'create'));
        return array('status' => 'success', 'message' => 'Item created successfully', 'id' => $conn->insert_id);
    } else {
        if (strpos($stmt->error, 'Duplicate entry') !== false) {
            http_response_code(400);
            return array('status' => 'error', 'message' => 'Product ID already exists');
        }
        http_response_code(500);
        return array(
            'status' => 'error', 
            'message' => 'Database error: ' . $stmt->error,
            'debug' => array(
                'product_id' => $product_id,
                'product_name' => $product_name,
                'quantity' => $quantity,
                'status' => $status,
                'warehouse' => $warehouse,
                'zone' => $zone,
                'row' => $row,
                'level' => $level,
                'location_id' => $location_id
            )
        );
    }
}

// UPDATE item
function update() {
    global $conn;
    
    $input = getInput();
    $input = convertInputFields($input);
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        return array('status' => 'error', 'message' => 'ID is required');
    }
    
    $id = intval($input['id']);
    $product_id = isset($input['product_id']) ? $input['product_id'] : null;
    $product_name = isset($input['product_name']) ? $input['product_name'] : null;
    $quantity = isset($input['quantity']) ? intval($input['quantity']) : null;
    $status = isset($input['status']) ? $input['status'] : null;
    $warehouse = isset($input['warehouse']) ? strtoupper($input['warehouse']) : null;
    $zone = isset($input['row_location']) ? strtoupper($input['row_location']) : null;
    $row = isset($input['column_location']) ? intval($input['column_location']) : null;
    $level = isset($input['level']) ? intval($input['level']) : null;
    
    // Build dynamic UPDATE query
    $updates = array();
    $params = array();
    $types = '';
    $location_id = null;
    
    if ($product_id !== null) {
        $updates[] = "product_id = ?";
        $params[] = $product_id;
        $types .= 's';
    }
    if ($product_name !== null) {
        if (!isAllowedProductName($product_name)) {
            http_response_code(400);
            return array('status' => 'error', 'message' => 'Product Name must be Paper, Wood, or Plastic');
        }
        $updates[] = "product_name = ?";
        $params[] = $product_name;
        $types .= 's';
    }
    if ($quantity !== null) {
        $updates[] = "quantity = ?";
        $params[] = $quantity;
        $types .= 'i';
    }
    if ($quantity !== null || $status !== null) {
        if ($quantity === null) {
            $sql_qty = "SELECT quantity FROM inventory WHERE id = ?";
            $stmt_qty = $conn->prepare($sql_qty);
            $stmt_qty->bind_param("i", $id);
            $stmt_qty->execute();
            $current_qty = $stmt_qty->get_result()->fetch_assoc();
            $quantity_for_status = $current_qty ? intval($current_qty['quantity']) : 0;
        } else {
            $quantity_for_status = $quantity;
        }
        $status = normalizeInventoryStatus($quantity_for_status, $status);
        $updates[] = "status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    // Handle location fields
    if ($warehouse !== null || $zone !== null || $row !== null || $level !== null) {
        // Get current values if not all are provided
        $sql_get = "SELECT warehouse, row_location, column_location, level FROM inventory WHERE id = ?";
        $stmt_get = $conn->prepare($sql_get);
        $stmt_get->bind_param("i", $id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        
        if ($result->num_rows > 0) {
            $current = $result->fetch_assoc();
            $warehouse = $warehouse ?? $current['warehouse'];
            $zone = $zone ?? $current['row_location'];
            $row = $row ?? $current['column_location'];
            $level = $level ?? $current['level'];
        }
        
        $location_error = validateLocation($warehouse, $zone, $row, $level);
        if ($location_error) {
            return array('status' => 'error', 'message' => $location_error);
        }
        
        $location_id = generateLocationId($warehouse, $zone, $row, $level);
        
        $updates[] = "warehouse = ?";
        $params[] = $warehouse;
        $types .= 's';
        
        $updates[] = "row_location = ?";
        $params[] = $zone;
        $types .= 's';
        
        $updates[] = "column_location = ?";
        $params[] = $row;
        $types .= 'i';
        
        $updates[] = "level = ?";
        $params[] = $level;
        $types .= 'i';
        
        $updates[] = "location_id = ?";
        $params[] = $location_id;
        $types .= 's';
    }
    
    if (empty($updates)) {
        http_response_code(400);
        return array('status' => 'error', 'message' => 'No fields to update');
    }
    
    $params[] = $id;
    $types .= 'i';
    
    $sql = "UPDATE inventory SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $log_type = isset($input['logType']) ? $input['logType'] : 'Inbound';
            $log_action = isset($input['logAction']) ? $input['logAction'] : 'Update Inventory Item';
            $log_source = isset($input['logSource']) ? $input['logSource'] : 'Inventory';
            $log_destination = isset($input['logDestination']) ? $input['logDestination'] : ($location_id ?? '');
            logOrderHistory($conn, $log_type, $log_action, 'Inventory', strval($id), $product_id ?? '', $product_name ?? '', $quantity ?? 0, $log_source, $log_destination, $status ?? 'Updated', '');
            emitLiveUpdate('inventory.changed', array('action' => 'update', 'id' => $id));
            emitLiveUpdate('history.changed', array('action' => 'update'));
            return array('status' => 'success', 'message' => 'Item updated successfully');
        } else {
            http_response_code(404);
            return array('status' => 'error', 'message' => 'Item not found');
        }
    } else {
        if (strpos($stmt->error, 'Duplicate entry') !== false) {
            http_response_code(400);
            return array('status' => 'error', 'message' => 'Product ID already exists');
        }
        http_response_code(500);
        return array('status' => 'error', 'message' => $stmt->error);
    }
}

// DELETE item
function delete() {
    global $conn;
    
    $input = getInput();
    
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        return array('status' => 'error', 'message' => 'ID is required');
    }
    
    $id = intval($input['id']);

    $item_sql = "SELECT product_id, product_name, quantity, location_id FROM inventory WHERE id = ?";
    $item_stmt = $conn->prepare($item_sql);
    $item_stmt->bind_param("i", $id);
    $item_stmt->execute();
    $item = $item_stmt->get_result()->fetch_assoc();
    
    $sql = "DELETE FROM inventory WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            if ($item) {
                logOrderHistory($conn, 'Outbound', 'Delete Inventory Item', 'Inventory', strval($id), $item['product_id'], $item['product_name'], $item['quantity'], $item['location_id'], 'Removed', 'Deleted', '');
            }
            emitLiveUpdate('inventory.changed', array('action' => 'delete', 'id' => $id));
            emitLiveUpdate('history.changed', array('action' => 'delete'));
            return array('status' => 'success', 'message' => 'Item deleted successfully');
        } else {
            http_response_code(404);
            return array('status' => 'error', 'message' => 'Item not found');
        }
    } else {
        http_response_code(500);
        return array('status' => 'error', 'message' => $stmt->error);
    }
}
?>
