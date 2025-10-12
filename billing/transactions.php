<?php
require_once('db.php');
session_start();

$filter_guest = $_GET['guest'] ?? '';
$filter_status = $_GET['status'] ?? '';
$filter_start = $_GET['start_date'] ?? '';
$filter_end = $_GET['end_date'] ?? '';

$query = "SELECT gb.*, CONCAT(g.first_name,' ',g.last_name) as guest_name
          FROM guest_billing gb
          JOIN guests g ON g.guest_id = gb.guest_id
          WHERE 1=1";

$params = [];

if ($filter_guest) {
    $query .= " AND (g.guest_id = ? OR CONCAT(g.first_name,' ',g.last_name) LIKE ?)";
    $params[] = $filter_guest;
    $params[] = "%$filter_guest%";
}

if ($filter_status) {
    $query .= " AND gb.payment_option = ?";
    $params[] = $filter_status;
}

if ($filter_start) {
    $query .= " AND gb.created_at >= ?";
    $params[] = $filter_start . ' 00:00:00';
}

if ($filter_end) {
    $query .= " AND gb.created_at <= ?";
    $params[] = $filter_end . ' 23:59:59';
}

$query .= " ORDER BY gb.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_by_status = ['Paid'=>0,'Partial Payment'=>0,'To be billed'=>0,'Refunded'=>0];
foreach ($transactions as $t) {
    if (isset($total_by_status[$t['payment_option']])) {
        $total_by_status[$t['payment_option']] += $t['amount'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Transactions</title>
<link rel="stylesheet" href="transactions.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.chart-popup {
    display: flex;
    justify-content: center;
    align-items: center;
    position: fixed;
    inset: 0;
    background-color: rgba(0,0,0,0.6);
    z-index: 9999;
}

.chart-container {
    background: #fff8f0;
    padding: 25px 30px;
    border-radius: 20px;
    text-align: center;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 5px 20px rgba(0,0,0,0.3);
}

.close-btn {
    position: absolute;
    top: 12px;
    right: 15px;
    background: #b89163;
    border: none;
    color: white;
    font-size: 16px;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}

.close-btn:hover {
    background: #a3784a;
}
</style>
</head>
<body>
<header>
    <div class="title-group">
        <div class="main-title">Hotel La Vista</div>
        <div class="subtitle">All Transactions</div>
    </div>
</header>

<form method="get" class="filter-form">
    <a href="http://localhost/hotel/billing/billing.php" class="header-btn">Back</a>
    <input type="text" name="guest" placeholder="Guest ID or Name" value="<?= htmlspecialchars($filter_guest) ?>">
    <select name="status">
        <option value="">All Status</option>
        <option value="Paid" <?= $filter_status==='Paid'?'selected':'' ?>>Paid</option>
        <option value="Partial Payment" <?= $filter_status==='Partial Payment'?'selected':'' ?>>Partial Payment</option>
        <option value="To be billed" <?= $filter_status==='To be billed'?'selected':'' ?>>To be billed</option>
        <option value="Refunded" <?= $filter_status==='Refunded'?'selected':'' ?>>Refunded</option>
    </select>
    <input type="date" name="start_date" value="<?= htmlspecialchars($filter_start) ?>">
    <input type="date" name="end_date" value="<?= htmlspecialchars($filter_end) ?>">
    <button type="submit">Filter</button>
    <button type="submit" name="export" value="1">Export to Excel</button>
</form>

<table class="transaction-table">
    <tr>
        <th>Order ID</th>
        <th>Guest</th>
        <th>Order Type</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Total Amount</th>
        <th>Partial Payment</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    <?php foreach($transactions as $t): ?>
    <tr>
        <td><?= $t['order_id'] ?></td>
        <td><?= htmlspecialchars($t['guest_name']) ?></td>
        <td><?= htmlspecialchars($t['order_type']) ?></td>
        <td><?= htmlspecialchars($t['item']) ?></td>
        <td><?= $t['quantity'] ?></td>
        <td>₱<?= number_format($t['amount'] ?? 0, 2) ?></td>
        <td>₱<?= number_format($t['partial_payment'] ?? 0, 2) ?></td>
        <td><?= htmlspecialchars($t['payment_option']) ?></td>
        <td><?= date('Y-m-d H:i', strtotime($t['created_at'])) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<div class="chart-popup" id="chartPopup">
    <div class="chart-container">
        <button class="close-btn" onclick="closeChart()">Close</button>
        <h3>Transaction Summary by Status</h3>
        <canvas id="popupChart"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('popupChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($total_by_status)) ?>,
        datasets: [{
            label: 'Total Amount by Status',
            data: <?= json_encode(array_values($total_by_status)) ?>,
            backgroundColor: ['#4caf50','#ffc107','#2196f3','#f44336']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: { y: { beginAtZero: true } }
    }
});

function closeChart() {
    document.getElementById('chartPopup').style.display = 'none';
}

window.onload = function() {
    document.getElementById('chartPopup').style.display = 'flex';
}
</script>
</body>
</html>
