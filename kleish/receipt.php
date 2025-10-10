<?php
session_start();
include 'kleishdb.php';

// Ensure `order_id` is provided and valid
$order_id = $_GET['order_id'] ?? 0;
$order_id = intval($order_id);

// Check if order_id is valid
if ($order_id <= 0) {
    echo "<div class='receipt-container'><p>Invalid Order ID.</p></div>";
    exit;
}

$order_query = $conn->prepare("SELECT o.*, u.username, u.customer_name 
                               FROM orders o 
                               LEFT JOIN users u ON o.customer_id = u.user_id 
                               WHERE o.order_id = ?");


if ($order_query === false) {
    die('MySQL prepare error: ' . $conn->error);  // Error if prepare fails
}

$order_query->bind_param("i", $order_id);
$order_query->execute();
$order_result = $order_query->get_result();

// Check if order exists
if ($order_result->num_rows === 0) {
    echo "<div class='receipt-container'><p>Order not found.</p></div>";
    exit;
}

$order = $order_result->fetch_assoc();

// Fetching order items
$item_query = $conn->prepare("SELECT oi.*, p.product_name 
                              FROM order_items oi 
                              LEFT JOIN products p ON oi.product_id = p.product_id
                              WHERE oi.order_id = ?");
if ($item_query === false) {
    die('MySQL prepare error: ' . $conn->error);  // Error if prepare fails
}

$item_query->bind_param("i", $order_id);
$item_query->execute();
$item_result = $item_query->get_result();

// Calculate subtotal, discount, and total
$subtotal = 0;
$discount = 0;
$discount_value = 0;
$final_total = 0;

// Check if session cart exists and has items
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['total'];
    }
    // Get discount from session or order
    $discount = $_SESSION['discount'] ?? $order['discount'];
    $discount_value = $subtotal * ($discount / 100);
    $final_total = $subtotal - $discount_value;
} else {
    // Use the order total if cart session is empty
    $final_total = $order['total']; 
}

// Current date and time
$date = date("Y-m-d H:i:s");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Receipt</title>
    <link rel="stylesheet" href="receipt.css">
</head>
<body onload="window.print()">
    <div class="receipt-container">
        <div class="header">
            <h1>Kleish Collection</h1>
            <p>Thank you for shopping with us 🌿</p>
        </div>

        <div class="info">
            <div><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></div>
            <div><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'Walk-in Customer') ?></div>
            <div><strong>User:</strong> <?= htmlspecialchars($order['username'] ?? 'Guest') ?></div>
            <div><strong>Date:</strong> <?= $date ?></div>
            <div><strong>Status:</strong> <?= htmlspecialchars($order['status'] ?? 'Pending') ?></div>
        </div>

        <div class="line"></div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th class="right">Qty</th>
                    <th class="right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $item_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="right"><?= $item['quantity'] ?></td>
                        <td class="right">₱<?= number_format($item['total'], 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="line"></div>

        <table>
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td class="right">₱<?= number_format($subtotal, 2) ?></td>
            </tr>
            <tr>
                <td><strong>Discount (<?= $discount ?>%):</strong></td>
                <td class="right">-₱<?= number_format($discount_value, 2) ?></td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td class="right"><strong>₱<?= number_format($final_total, 2) ?></strong></td>
            </tr>
        </table>

        <div class="line"></div>
        <div class="footer">
            Thank you for your purchase!<br>
            kleishcollection.com
        </div>

        <!-- Optional Manual Print Button -->
        <button onclick="window.print()">🖨️ Print Receipt</button>

    </div>
</body>
</html>
