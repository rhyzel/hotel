<?php
require_once('../db.php');
session_start();

$order = $_SESSION['order_giftstore'] ?? [];
$order_id = rand(1000, 9999);

$guest_id = $_POST['guest_id'] ?? null;
$guest_name = $_POST['guest_name'] ?? 'Walk-in Guest';
$order_type = 'Gift Store';
$payment_option_input = $_POST['payment_option'] ?? 'bill';
$payment_method = $_POST['payment_method'] ?? null;
$partial_payment = floatval($_POST['partial_payment'] ?? 0);
$order_notes = $_POST['order_notes'] ?? '';

$room_number = null;
if ($guest_id) {
    $stmt = $conn->prepare("
        SELECT rm.room_number
        FROM reservations r
        LEFT JOIN rooms rm ON r.room_id = rm.room_id
        WHERE r.guest_id = ? AND r.status = 'checked_in'
        ORDER BY r.check_in DESC
        LIMIT 1
    ");
    $stmt->execute([$guest_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($room) $room_number = $room['room_number'];
}

$total = 0;
foreach ($order as $item) {
    $qty = intval($item['qty']);
    if ($qty <= 0) continue;
    $total += floatval($item['price']) * $qty;
}

if ($payment_option_input === 'upfront' && $partial_payment >= $total) {
    $payment_option = 'Paid';
    $paid_amount = $total;
    $remaining_amount = 0;
} elseif ($payment_option_input === 'upfront' && $partial_payment > 0 && $partial_payment < $total) {
    $payment_option = 'Partial Payment';
    $paid_amount = $partial_payment;
    $remaining_amount = $total - $partial_payment;
} else {
    $payment_option = 'To be billed';
    $paid_amount = 0;
    $remaining_amount = $total;
    $payment_method = null;
}

foreach ($order as $id => $item) {
    $qty = intval($item['qty']);
    if ($qty <= 0) continue;

    $price = floatval($item['price']);
    $subtotal = $price * $qty;

    $item_partial = 0;
    $item_remaining = $subtotal;

    if ($payment_option === 'Partial Payment' && $total > 0) {
        $item_partial = ($subtotal / $total) * $partial_payment;
        $item_remaining = $subtotal - $item_partial;
    } elseif ($payment_option === 'Paid') {
        $item_partial = $subtotal;
        $item_remaining = 0;
    }

    $stmt = $conn->prepare("UPDATE inventory SET quantity_in_stock = quantity_in_stock - ? WHERE item_id = ?");
    $stmt->execute([$qty, $item['id'] ?? $id]);

    $stmt = $conn->prepare("
        INSERT INTO guest_billing 
        (guest_id, guest_name, order_type, item, order_id, amount, quantity, payment_option, payment_method, partial_payment, remaining_amount, remaining_total, created_at, updated_at, total_amount)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), ?)
    ");
    $stmt->execute([
        $guest_id,
        $guest_name,
        $order_type,
        $item['name'],
        $order_id,
        $subtotal,
        $qty,
        $payment_option,
        $payment_method,
        $item_partial,
        $item_remaining,
        $remaining_amount,
        $total
    ]);

    $stmt = $conn->prepare("SELECT category FROM inventory WHERE item_id = ?");
    $stmt->execute([$item['id'] ?? $id]);
    $category_row = $stmt->fetch(PDO::FETCH_ASSOC);
    $category = $category_row['category'] ?? 'Uncategorized';

    $used_by = $guest_name;
    $stmt = $conn->prepare("
        INSERT INTO stock_usage
        (order_id, item, category, guest_id, guest_name, quantity_used, used_by, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt->execute([$order_id, $item['name'], $category, $guest_id, $guest_name, $qty, $used_by]);
}

$_SESSION['order_giftstore'] = [];
$receipt_date = date('F j, Y, g:i A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - Hotel La Vista</title>
<link rel="stylesheet" href="add_order.css">
</head>
<body>
<h2>Hotel La Vista</h2>
<p>Date: <?= htmlspecialchars($receipt_date) ?></p>
<p>Guest: <?= htmlspecialchars($guest_name) ?><?= $room_number ? " | Room $room_number" : "" ?></p>
<p>Order Type: <?= htmlspecialchars($order_type) ?></p>
<p>Payment Method: <?= htmlspecialchars($payment_option === 'Paid' || $payment_option === 'Partial Payment' ? ($payment_method ?? '-') : 'To be billed') ?></p>
<?php if (!empty($order_notes)): ?>
<p>Notes: <?= htmlspecialchars($order_notes) ?></p>
<?php endif; ?>
<table>
<thead>
<tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
</thead>
<tbody>
<?php foreach ($order as $item):
    $qty = intval($item['qty']);
    if ($qty <= 0) continue;
    $price = floatval($item['price']);
    $subtotal = $price * $qty;
?>
<tr>
<td><?= htmlspecialchars($item['name']) ?></td>
<td><?= $qty ?></td>
<td>₱<?= number_format($price, 2) ?></td>
<td>₱<?= number_format($subtotal, 2) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr><td colspan="3">Total</td><td>₱<?= number_format($total, 2) ?></td></tr>
<?php if ($payment_option === 'Paid' || $payment_option === 'Partial Payment'): ?>
<tr><td colspan="3">Paid</td><td>₱<?= number_format($paid_amount, 2) ?></td></tr>
<tr><td colspan="3">Remaining</td><td>₱<?= number_format($remaining_amount, 2) ?></td></tr>
<?php else: ?>
<tr><td colspan="3">Remaining</td><td>₱<?= number_format($remaining_amount, 2) ?></td></tr>
<?php endif; ?>
</tfoot>
</table>
<div class="print-btn"><button onclick="window.print()">Print Receipt</button></div>
<div class="back-btn"><a href="giftstore_pos.php"><button>Back to POS</button></a></div>
</body>
</html>
