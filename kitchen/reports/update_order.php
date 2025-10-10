<?php
require_once(__DIR__ . '/../utils/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    $assignedChef = $_POST['assigned_chef'] ?? null;
    $action = $_POST['action'] ?? null;
    $remark = $_POST['remark'] ?? null;
    $complainReason = $_POST['complain_reason'] ?? null;

    if ($orderId) {
        $stmt = $pdo->prepare("UPDATE kitchen_orders SET assigned_chef = ?, notes = ?, complain_reason = ?, updated_at = NOW() WHERE order_id = ?");
        $stmt->execute([$assignedChef, $remark, $complainReason, $orderId]);

        if ($action === 'refund') {
            $stmtRefund1 = $pdo->prepare("UPDATE kitchen_orders SET resolution = 'refund_requested' WHERE order_id = ?");
            $stmtRefund1->execute([$orderId]);

            $stmtRefund2 = $pdo->prepare("UPDATE order_items SET status = 'refund_requested' WHERE order_id = ?");
            $stmtRefund2->execute([$orderId]);
        } elseif ($action === 'replacement') {
            $stmtReplacement1 = $pdo->prepare("UPDATE kitchen_orders SET resolution = 'replacement_requested' WHERE order_id = ?");
            $stmtReplacement1->execute([$orderId]);

            $stmtReplacement2 = $pdo->prepare("UPDATE order_items SET status = 'replacement_requested' WHERE order_id = ?");
            $stmtReplacement2->execute([$orderId]);

            $stmtOrder = $pdo->prepare("INSERT INTO kitchen_orders (order_type, table_number, room_id, guest_name, guest_id, status, total_amount, notes, created_at, updated_at) 
                SELECT order_type, table_number, room_id, guest_name, guest_id, 'preparing', total_amount, notes, NOW(), NOW() 
                FROM kitchen_orders WHERE order_id = ?");
            $stmtOrder->execute([$orderId]);

            $newOrderId = $pdo->lastInsertId();

            $stmtItems = $pdo->prepare("INSERT INTO order_items (order_id, recipe_id, quantity, status) 
                SELECT ?, recipe_id, quantity, 'preparing' FROM order_items WHERE order_id = ?");
            $stmtItems->execute([$newOrderId, $orderId]);
        }
    }
}

header("Location: order_reports.php");
exit;
?>
