<?php
require_once('db.php');
$pdo = $conn;

$category = $_GET['category'] ?? '';
$order_id = $_GET['order_id'] ?? '';

$categories = [
  'giftstore' => 'Gift Store',
  'minibar' => 'Mini Bar',
  'loungebar' => 'Lounge Bar'
];

if (!isset($categories[$category]) || !$order_id) {
    echo json_encode([]);
    exit;
}

$order_type = $categories[$category];

$stmt = $pdo->prepare("
    SELECT item 
    FROM guest_billing 
    WHERE order_id = ? 
      AND order_type = ? 
      AND item NOT IN (SELECT reported_item FROM reported_items WHERE order_id = ?)
");
$stmt->execute([$order_id, $order_type, $order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($items);
