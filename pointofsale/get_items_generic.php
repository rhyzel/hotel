<?php
require_once('db.php');
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id > 0) {
    $stmt = $conn->prepare("
        SELECT item
        FROM guest_billing
        WHERE order_id = ?
          AND order_type IN ('Gift Store','Mini Bar','Lounge Bar')
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
} else {
    echo json_encode([]);
}
