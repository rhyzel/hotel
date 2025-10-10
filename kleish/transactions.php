<?php
session_start();
include_once 'kleishdb.php'; // Use include_once to avoid including the same file multiple times

$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Admin';

$filterCustomer = isset($_GET['customer']) ? $_GET['customer'] : '';
$filterDate = isset($_GET['date']) ? $_GET['date'] : '';

$transactions = [];
$query = "
    SELECT order_id, customer_name, total, payment_method, order_date 
    FROM orders 
    WHERE 1=1";

if (!empty($filterCustomer)) {
    $query .= " AND customer_name LIKE '%" . mysqli_real_escape_string($conn, $filterCustomer) . "%'";
}
if (!empty($filterDate)) {
    $query .= " AND DATE(order_date) = '" . mysqli_real_escape_string($conn, $filterDate) . "'";
}

$query .= "
    UNION ALL
    SELECT order_id, customer_name, total, payment_method, order_date 
    FROM pos_orders 
    WHERE 1=1";

if (!empty($filterCustomer)) {
    $query .= " AND customer_name LIKE '%" . mysqli_real_escape_string($conn, $filterCustomer) . "%'";
}
if (!empty($filterDate)) {
    $query .= " AND DATE(order_date) = '" . mysqli_real_escape_string($conn, $filterDate) . "'";
}

$query .= " ORDER BY order_date DESC";

$result = mysqli_query($conn, $query);
if (!$result) die("Transaction Query Failed: " . mysqli_error($conn));
while ($row = mysqli_fetch_assoc($result)) {
    $transactions[] = $row;
}

// Export CSV
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=transactions.csv');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'Customer', 'Total Amount', 'Payment Method', 'Date']);
    foreach ($transactions as $t) {
        fputcsv($output, [$t['order_id'], $t['customer_name'], $t['total'], $t['payment_method'], $t['order_date']]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction History - Kleish Collection</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="transaction_history.css">
</head>
<body>



<div class="main-content">

    <h1>Transaction History</h1>

    <form method="get" style="margin-bottom: 15px; display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
        <input type="text" name="customer" placeholder="Filter by customer name" value="<?php echo htmlspecialchars($filterCustomer); ?>">
        <input type="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>">
        <button type="submit" style="padding: 6px 12px;">Filter</button>
        <a href="?export=csv&customer=<?php echo urlencode($filterCustomer); ?>&date=<?php echo urlencode($filterDate); ?>" 
           style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">
           Export CSV
        </a>
      </form>

  <div class="dashboard">
    <div class="card" style="overflow-x:auto;">
      <h3>All Transactions</h3>
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
          <?php if (count($transactions) > 0): ?>
            <?php foreach ($transactions as $t): ?>
              <tr>
                <td><?php echo htmlspecialchars($t['order_id']); ?></td>
                <td><?php echo htmlspecialchars($t['customer_name']); ?></td>
                <td>₱<?php echo number_format($t['total'], 2); ?></td>
                <td><?php echo htmlspecialchars($t['payment_method']); ?></td>
                <td><?php echo date("F j, Y g:i A", strtotime($t['order_date'])); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No transactions found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      
      

    </div>
  </div>


</div>

</body>
</html>
