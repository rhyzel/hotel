<?php
require_once('../db.php');
header('Content-Type: application/json');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_id > 0) {
    $stmt = $conn->prepare("
        SELECT gb.item, gb.amount, gb.partial_payment, gb.remaining_amount
        FROM guest_billing gb
        LEFT JOIN reported_order ro 
            ON gb.order_id = ro.order_id 
            AND gb.item = ro.item
        WHERE gb.order_id = ? 
          AND gb.amount > gb.partial_payment 
          AND ro.id IS NULL
    ");
    $stmt->execute([$order_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($items);
} else {
    echo json_encode([]);
}
?>
