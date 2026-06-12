<?php
function readJsonInput() {
    $raw = file_get_contents("php://input");
    if (!$raw) {
        return array();
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : array();
}

function jsonResponse($payload, $code = 200) {
    http_response_code($code);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit();
}

function logOrderHistory($conn, $log_type, $action, $reference_type, $reference_id, $product_id, $product_name, $quantity, $source, $destination, $status, $note = '') {
    $sql = "INSERT INTO OrderHis (log_type, action, reference_type, reference_id, product_id, product_name, quantity, source, destination, status, note)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }

    $quantity = intval($quantity);
    $stmt->bind_param(
        "ssssssissss",
        $log_type,
        $action,
        $reference_type,
        $reference_id,
        $product_id,
        $product_name,
        $quantity,
        $source,
        $destination,
        $status,
        $note
    );

    return $stmt->execute();
}

function emitLiveUpdate($topic, $payload = array()) {
    $message = json_encode(array(
        'topic' => $topic,
        'payload' => $payload,
        'sentAt' => gmdate('c')
    ), JSON_UNESCAPED_UNICODE);

    $fp = @fsockopen('127.0.0.1', 8090, $errno, $errstr, 0.25);
    if (!$fp) {
        return false;
    }

    $request = "POST /broadcast HTTP/1.1\r\n";
    $request .= "Host: 127.0.0.1:8090\r\n";
    $request .= "Content-Type: application/json; charset=utf-8\r\n";
    $request .= "Content-Length: " . strlen($message) . "\r\n";
    $request .= "Connection: Close\r\n\r\n";
    $request .= $message;

    fwrite($fp, $request);
    fclose($fp);
    return true;
}
?>
