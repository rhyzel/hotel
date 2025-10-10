<?php
require_once(__DIR__ . '/../utils/db.php');

$order_id = $_GET['order_id'] ?? '';
if ($order_id) {
    $stmt = $pdo->prepare("SELECT item_name FROM kitchen_orders WHERE order_id=?");
    $stmt->execute([$order_id]);
    $allItems = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items = array_map('trim', explode(',', $row['item_name']));
        $allItems = array_merge($allItems, $items);
    }

    echo json_encode($allItems);
}
?>
