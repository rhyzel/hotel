<?php

// Start session to handle user login or admin details
session_start();
include_once 'kleishdb.php'; // Database connection file

// Retrieve the logged-in user name, fallback to 'Admin' if not logged in
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';

// Get any filter parameters from the URL (optional)
$filterCustomer = isset($_GET['customer']) ? $_GET['customer'] : '';
$filterDate = isset($_GET['date']) ? $_GET['date'] : '';

// Initialize an empty array to hold the order data
$orders = [];

// Start building the SQL query
$query = "SELECT order_id, customer_name, total, payment_method, order_date FROM orders";

// Apply filters if provided by the user
$conditions = []; // Array to hold conditions for WHERE clause

if (!empty($filterCustomer)) {
    $conditions[] = "customer_name LIKE '%" . mysqli_real_escape_string($conn, $filterCustomer) . "%'";
}
if (!empty($filterDate)) {
    $conditions[] = "DATE(order_date) = '" . mysqli_real_escape_string($conn, $filterDate) . "'";
}

// If there are conditions, add them to the WHERE clause
if (count($conditions) > 0) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

// Order by date
$query .= " ORDER BY order_date DESC"; // Order by date, most recent first

// Execute the query
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Order Query Failed: " . mysqli_error($conn));
}

// Fetch the results into the $orders array
while ($row = mysqli_fetch_assoc($result)) {
    $orders[] = $row;
}

// Export to CSV functionality (optional)
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=tiktok_orders.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'Customer Name', 'Total Amount', 'Payment Method', 'Order Date']);
    foreach ($orders as $order) {
        fputcsv($output, [$order['order_id'], $order['customer_name'], $order['total'], $order['payment_method'], $order['order_date']]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TikTok Orders - Kleish Collection</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="orders_tracking.css">
</head>
<body>

<div class="main-content">
    <h1>TikTok Orders</h1>

    <a href="https://www.tiktok.com/business/dashboard" class="btn" target="_blank">
    Go to TikTok Shop Dashboard
</a>

</div>
    <form method="get" style="margin-bottom: 15px; display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                <input type="text" name="customer" placeholder="Filter by customer name" value="<?php echo htmlspecialchars($filterCustomer); ?>">
                <input type="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>">
                <button type="submit" style="padding: 6px 12px;">Filter</button>
                <a href="?export=csv&customer=<?php echo urlencode($filterCustomer); ?>&date=<?php echo urlencode($filterDate); ?>" 
                   style="padding: 6px 12px; background:rgb(132, 133, 77); color: white; text-decoration: none; border-radius: 4px; margin-top: -20px;">
                   Export CSV
                </a>
            </form>

    <div class="dashboard">
        <div class="card" style="overflow-x:auto;">
            <h3>All Orders</h3>
            <table style="width:100%;border-collapse:collapse;text-align:left;">
                <thead>
                    <tr style="background:#f2f2f2;">
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orders) > 0): ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>₱<?php echo number_format($order['total'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                <td><?php echo date("F j, Y g:i A", strtotime($order['order_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No orders found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>


        </div>
    </div>

</div>

</body>
</html>
