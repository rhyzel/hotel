<?php
require_once('../db.php');
session_start();

$order = $_SESSION['order_restaurant'] ?? [];
if (empty($order)) {
    header("Location: restaurant_pos.php");
    exit;
}

$order_id = rand(1000, 9999);
$guest_id = $_POST['guest_id'] ?? null;
$guest_name = $_POST['guest_name'] ?? '';
$delivery_type = $_POST['delivery_type'] ?? 'Restaurant';
$table_number = $_POST['table_number'] ?? null;
$room_number = $_POST['room_number'] ?? null;

if ($delivery_type === 'Room Service') {
    $order_type = 'Room Service';
    $table_number = null;
} else {
    $order_type = 'Restaurant';
    $room_number = null;
}

$payment_option_input = $_POST['payment_option'] ?? 'bill';
$payment_method = $_POST['payment_method'] ?? null;
$partial_payment = floatval($_POST['partial_payment'] ?? 0);
$order_notes = $_POST['order_notes'] ?? '';

$total = 0;
foreach ($order as $item) {
    $qty = intval($item['qty']);
    $price = floatval($item['price']);
    $total += $qty * $price;
}

$items = array_map(fn($i) => $i['name'], $order);
$item_quantities = array_map(fn($i) => $i['qty'], $order);
$item_str = implode(", ", $items);
$quantity_str = implode(", ", $item_quantities);

$stmtInsert = $conn->prepare("
    INSERT INTO kitchen_orders
    (order_id, order_type, status, table_number, room_number, guest_name, guest_id, item, quantity, order_notes, total_amount, created_at, updated_at)
    VALUES (?, ?, 'preparing', ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
");
$stmtInsert->execute([
    $order_id,
    $order_type,
    $table_number,
    $room_number,
    $guest_name,
    $guest_id,
    $item_str,
    $quantity_str,
    $order_notes,
    $total
]);

$paid_so_far = 0;
$count = count($order);
$current_index = 0;
$remaining_total = max($total - $partial_payment, 0);

foreach ($order as $item) {
    $qty = intval($item['qty']);
    $price = floatval($item['price']);
    $subtotal = $qty * $price;

    if ($payment_option_input === 'upfront') {
        $paid_amount = round(($subtotal / $total) * $partial_payment, 2);
        $remaining_amount = $subtotal - $paid_amount;
        $payment_option = 'Partial Payment';
    } elseif ($payment_option_input === 'refund') {
        $paid_amount = 0;
        $remaining_amount = 0;
        $payment_option = 'Refunded';
    } else {
        $paid_amount = 0;
        $remaining_amount = $subtotal;
        $payment_option = 'To be billed';
    }

    $paid_so_far += $paid_amount;
    $current_index++;

    if ($current_index === $count && $payment_option_input === 'upfront') {
        $diff = $partial_payment - $paid_so_far;
        $remaining_amount -= $diff;
    }

    $stmtBill = $conn->prepare("
        INSERT INTO guest_billing
        (guest_id, guest_name, order_type, item, order_id, amount, quantity, payment_option, payment_method, partial_payment, remaining_amount, remaining_total, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    $stmtBill->execute([
        $guest_id,
        $guest_name,
        $order_type,
        $item['name'],
        $order_id,
        $subtotal,
        $qty,
        $payment_option,
        $payment_method,
        $paid_amount,
        $remaining_amount,
        $remaining_total
    ]);
}

foreach ($order as $item) {
    $recipe_name = $item['name'];
    $qty_ordered = intval($item['qty']);
    $used_by = $guest_name ?: 'Restaurant Guest';

    $stmtRecipe = $conn->prepare("SELECT id FROM recipes WHERE recipe_name = ?");
    $stmtRecipe->execute([$recipe_name]);
    $recipe = $stmtRecipe->fetch(PDO::FETCH_ASSOC);

    if ($recipe) {
        $recipe_id = $recipe['id'];
        $stmtIngredients = $conn->prepare("
            SELECT ingredient_name, category, quantity_needed, unit 
            FROM ingredients 
            WHERE recipe_id = ?
        ");
        $stmtIngredients->execute([$recipe_id]);
        $ingredients = $stmtIngredients->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ingredients as $ingredient) {
            $ingredient_name = $ingredient['ingredient_name'];
            $category = $ingredient['category'] ?: null;
            $used_qty = floatval($ingredient['quantity_needed']) * $qty_ordered;

            $stmtInv = $conn->prepare("SELECT item_id, quantity_in_stock, category FROM inventory WHERE item = ?");
            $stmtInv->execute([$ingredient_name]);
            $invItem = $stmtInv->fetch(PDO::FETCH_ASSOC);

            if ($invItem) {
                $item_id = $invItem['item_id'];
                $new_qty = max($invItem['quantity_in_stock'] - $used_qty, 0);
                if (!$category) {
                    $category = $invItem['category'];
                }
                if (!$category) {
                    $category = 'Others';
                }

                $stmtUpdateInv = $conn->prepare("
                    UPDATE inventory 
                    SET quantity_in_stock = ?, used_qty = used_qty + ? 
                    WHERE item_id = ?
                ");
                $stmtUpdateInv->execute([$new_qty, $used_qty, $item_id]);

                $stmtUsage = $conn->prepare("
                    INSERT INTO stock_usage 
                    (order_id, item, category, guest_id, guest_name, quantity_used, used_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmtUsage->execute([
                    $order_id,
                    $ingredient_name,
                    $category,
                    $guest_id,
                    $guest_name,
                    $used_qty,
                    $used_by
                ]);
            }
        }
    }
}

$receipt_date = date('F j, Y, g:i A');
$_SESSION['order_restaurant'] = [];
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
<p>Guest: <?= htmlspecialchars($guest_name) ?></p>
<p>
<?php
if ($order_type === 'Room Service' && !empty($room_number)) {
    echo "Room: " . htmlspecialchars($room_number);
} elseif ($order_type === 'Restaurant' && !empty($table_number)) {
    echo "Table: " . htmlspecialchars($table_number);
} else {
    echo "-";
}
?>
</p>
<p>Order Type: <?= htmlspecialchars($order_type) ?></p>
<p>Payment Method: <?= htmlspecialchars($payment_method ?? '-') ?></p>
<?php if(!empty($order_notes)): ?>
<p>Notes: <?= htmlspecialchars($order_notes) ?></p>
<?php endif; ?>
<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($order as $item):
            $qty = intval($item['qty']);
            if ($qty <= 0) continue;
            $price = floatval($item['price']);
            $subtotal = $qty * $price;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $qty ?></td>
            <td>₱<?= number_format($price,2) ?></td>
            <td>₱<?= number_format($subtotal,2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Total</td>
            <td>₱<?= number_format($total,2) ?></td>
        </tr>
        <?php if($payment_option_input === 'upfront'): ?>
        <tr>
            <td colspan="3">Paid</td>
            <td>₱<?= number_format($partial_payment,2) ?></td>
        </tr>
        <tr>
            <td colspan="3">Remaining</td>
            <td>₱<?= number_format($remaining_total,2) ?></td>
        </tr>
        <?php elseif($payment_option_input === 'refund'): ?>
        <tr>
            <td colspan="3">Refunded</td>
            <td>₱0.00</td>
        </tr>
        <?php else: ?>
        <tr>
            <td colspan="3">Remaining</td>
            <td>₱<?= number_format($total,2) ?></td>
        </tr>
        <?php endif; ?>
    </tfoot>
</table>
<div class="print-btn">
    <button onclick="window.print()">Print Receipt</button>
</div>
<div class="back-btn">
    <a href="restaurant_pos.php"><button>Back to POS</button></a>
</div>
</body>
</html>
