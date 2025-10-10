<?php
require_once('../db.php');
session_start();

$order = $_SESSION['order_lounge'] ?? [];
$order_id = $_POST['order_id'] ?? rand(1000,9999);

$guest_id = $_POST['guest_id'] ?? null;
$guest_name = $_POST['guest_name'] ?? 'Walk-in Guest';
$order_type = 'Lounge Bar';
$payment_option_input = $_POST['payment_option'] ?? 'bill';
$payment_method_input = $_POST['payment_method'] ?? null;
$partial_payment_input = floatval($_POST['partial_payment'] ?? 0);
$order_notes = $_POST['order_notes'] ?? '';

$total = 0;
$receipt_items = [];

foreach($order as $id => $item){
    $qty = intval($item['qty']);
    $price = floatval($item['price']);
    if($qty <= 0 || $price <= 0) continue;

    $subtotal = $price * $qty;
    $total += $subtotal;

    $receipt_items[] = [
        'id' => $id,
        'name' => $item['name'],
        'qty' => $qty,
        'price' => $price,
        'subtotal' => $subtotal
    ];

    $stmt_inv = $conn->prepare("UPDATE inventory SET quantity_in_stock = quantity_in_stock - ? WHERE item_id = ?");
    $stmt_inv->execute([$qty, $id]);

    $stmt_stock_usage = $conn->prepare("
        INSERT INTO stock_usage
        (order_id, item, guest_id, guest_name, quantity_used, used_by, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");
    $stmt_stock_usage->execute([$order_id, $item['name'], $guest_id, $guest_name, $qty, $guest_name]);
}

$paid_amount = 0;
$remaining_amount = $total;
$current_payment_option = 'To be billed';
$current_payment_method = null;
$current_partial_payment = 0;

if($payment_option_input === 'upfront'){
    $current_partial_payment = min($partial_payment_input, $total);
    $paid_amount = $current_partial_payment;
    $remaining_amount = $total - $paid_amount;
    $current_payment_method = $payment_method_input;
    
    if($paid_amount === $total){
        $current_payment_option = 'Paid';
    } elseif($paid_amount > 0){
        $current_payment_option = 'Partial Payment';
    }
}

foreach($receipt_items as $item){
    $stmt = $conn->prepare("
        INSERT INTO guest_billing 
        (guest_id, guest_name, order_type, item, order_id, amount, quantity, total_amount, payment_option, payment_method, partial_payment, remaining_amount, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $item_partial_payment = ($current_payment_option === 'Partial Payment') ? round($item['subtotal'] / $total * $paid_amount, 2) : ($current_payment_option === 'Paid' ? $item['subtotal'] : 0);
    $item_remaining = $item['subtotal'] - $item_partial_payment;

    $stmt->execute([
        $guest_id,
        $guest_name,
        $order_type,
        $item['name'],
        $order_id,
        $item['price'],
        $item['qty'],
        $item['subtotal'],
        $current_payment_option,
        $current_payment_method,
        $item_partial_payment,
        $item_remaining
    ]);
}

$_SESSION['order_lounge'] = [];

$receipt_date = date('F j, Y, g:i A');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - Hotel La Vista</title>
<link rel="stylesheet" href="loungebar_pos.css">
</head>
<body>
<h2>Hotel La Vista</h2>
<p>Date: <?= htmlspecialchars($receipt_date) ?></p>
<p>Guest: <?= htmlspecialchars($guest_name) ?></p>
<p>Order Type: <?= htmlspecialchars($order_type) ?></p>
<p>Payment Method: <?= htmlspecialchars($current_payment_method ?? 'To be billed') ?></p>
<?php if(!empty($order_notes)): ?><p>Notes: <?= htmlspecialchars($order_notes) ?></p><?php endif; ?>

<table>
<thead>
<tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
</thead>
<tbody>
<?php foreach($receipt_items as $item): ?>
<tr>
<td><?= htmlspecialchars($item['name']) ?></td>
<td><?= $item['qty'] ?></td>
<td>₱<?= number_format($item['price'],2) ?></td>
<td>₱<?= number_format($item['subtotal'],2) ?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr><td colspan="3">Total</td><td>₱<?= number_format($total,2) ?></td></tr>
<?php if($current_payment_option === 'Paid' || $current_payment_option === 'Partial Payment'): ?>
<tr><td colspan="3">Paid</td><td>₱<?= number_format($paid_amount,2) ?></td></tr>
<tr><td colspan="3">Remaining</td><td>₱<?= number_format($remaining_amount,2) ?></td></tr>
<?php else: ?>
<tr><td colspan="3">Remaining</td><td>₱<?= number_format($remaining_amount,2) ?></td></tr>
<?php endif; ?>
</tfoot>
</table>
<div class="print-btn"><button onclick="window.print()">Print Receipt</button></div>
<div class="back-btn"><a href="loungebar_pos.php"><button>Back to POS</button></a></div>
</body>
</html>
