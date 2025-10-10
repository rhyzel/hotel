<?php
session_start();
include 'kleishdb.php';

if ($conn === null || $conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['employee_id'])) {
    header("Location: login.php");
    exit;
}

$employeeId = $_SESSION['employee_id'];

$query = "SELECT full_name FROM users WHERE employee_id = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("s", $employeeId);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();

if (!$first_name) {
    die("Employee name not found.");
}

$totalUsers = 0;
$employeeQuery = "SELECT COUNT(*) AS total FROM users";
if ($employeeResult = $conn->query($employeeQuery)) {
    $totalUsers = $employeeResult->fetch_assoc()['total'];
} else {
    die("Users Query Error: " . $conn->error);
}

$totalOrders = 0;
$ordersQuery = "SELECT COUNT(*) AS total FROM orders";
if ($ordersResult = $conn->query($ordersQuery)) {
    $totalOrders = $ordersResult->fetch_assoc()['total'];
} else {
    die("Orders Query Error: " . $conn->error);
}

$todaySales = 0.00;
$salesQuery = "SELECT SUM(total) AS total FROM orders WHERE DATE(order_date) = CURDATE()";
if ($salesResult = $conn->query($salesQuery)) {
    $todaySales = number_format($salesResult->fetch_assoc()['total'] ?? 0, 2);
} else {
    die("Sales Query Error: " . $conn->error);
}

$lowStockCount = 0;
$stockQuery = "SELECT COUNT(*) AS count FROM products WHERE stock <= 5";
if ($stockResult = $conn->query($stockQuery)) {
    $lowStockCount = $stockResult->fetch_assoc()['count'];
} else {
    die("Inventory Stock Query Error: " . $conn->error);
}

$pendingOrders = 0;
$orderQuery = "SELECT COUNT(*) AS count FROM orders WHERE status = 'pending'";
if ($orderResult = $conn->query($orderQuery)) {
    $pendingOrders = $orderResult->fetch_assoc()['count'];
} else {
    die("Order Query Error: " . $conn->error);
}

$popularCategories = [];
$categoryQuery = "
    SELECT c.category_name AS category, COUNT(o.order_id) AS total_orders
    FROM orders o
    INNER JOIN products p ON o.product_id = p.product_id
    INNER JOIN categories c ON p.category_id = c.category_id
    WHERE o.status = 'completed'
    GROUP BY c.category_name
    ORDER BY total_orders DESC
    LIMIT 2
";
if ($categoryResult = $conn->query($categoryQuery)) {
    while ($row = $categoryResult->fetch_assoc()) {
        $popularCategories[] = $row['category'];
    }
} else {
    die("Category Query Error: " . $conn->error);
}

$weeklySalesData = [];
$labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D', strtotime($date));
    $query = "SELECT SUM(total) as total FROM orders WHERE DATE(order_date) = '$date'";
    if ($result = $conn->query($query)) {
        $data = $result->fetch_assoc();
        $weeklySalesData[] = $data['total'] ?? 0;
    } else {
        die("Weekly Sales Query Error: " . $conn->error);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kleish Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="dashboard.css"/>
</head>
<body>
<div class="sidebar" id="sidebar" onmouseenter="expandSidebar()" onmouseleave="collapseSidebar()">
  <div class="logo-container">
    <img src="kleishlogo.png" alt="Kleish Collection Logo">
  </div>
  <a href="inventory.php"><i class="fas fa-boxes"></i><span class="label"> Inventory Management</span></a>
  <a href="kleish_pos.php"><i class="fas fa-cash-register"></i><span class="label"> Point of Sale (POS)</span></a>
  <a href="transactions.php"><i class="fas fa-receipt"></i><span class="label"> Transaction History</span></a>
  <a href="orders.php"><i class="fas fa-truck-loading"></i><span class="label"> Order Tracking</span></a>
  <a href="customer_feedback.php"><i class="fas fa-users"></i><span class="label"> Customer Insights</span></a>
  <a href="marketing.php"><i class="fas fa-bullhorn"></i><span class="label"> Marketing Tools</span></a>
  <a href="staff.php"><i class="fas fa-user-cog"></i><span class="label"> Staff Management</span></a>
  <a href="employee_profile.php"><i class="fas fa-user-circle"></i><span class="label"> Employee Profile</span></a>
  <a href="analytics.php"><i class="fas fa-chart-line"></i><span class="label"> Reports and Analytics</span></a>
  <hr>
  <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="label"> Logout</span></a>
</div>

<div class="main-content" id="main-content">
  <header>
    <h1>Welcome, <?= htmlspecialchars($first_name); ?>!</h1>
  </header>
  <div class="dashboard">
    <div class="card"><h3>Today’s Sales</h3><p>₱<?= $todaySales; ?></p></div>
    <div class="card"><h3>Low Stock Alerts</h3><p><?= $lowStockCount; ?> items low</p></div>
    <div class="card"><h3>Pending Orders</h3><p><?= $pendingOrders; ?> awaiting</p></div>
    <div class="card"><h3>Popular Categories</h3><p><?= implode(", ", $popularCategories); ?></p></div>
    <div class="card" style="padding: 1rem 1rem 2rem;">
      <h3>Weekly Sales Overview</h3>
      <canvas id="weeklySalesChart" height="200"></canvas>
    </div>
    <div class="card">
      <h3>Quick Actions</h3>
      <ul style="list-style:none;padding:0;text-align:left;">
        <li><i class="fas fa-plus-circle"></i> <a href="inventory.php">Inventory</a></li>
        <li><i class="fas fa-truck"></i> <a href="orders.php">View All Orders</a></li>
        <li><i class="fas fa-chart-bar"></i> <a href="analytics.php">View Reports</a></li>
        <li><i class="fas fa-tags"></i> <a href="staff.php">Employee List</a></li>
      </ul>
    </div>
  </div>
</div>

<script>
function expandSidebar() {
  document.getElementById('sidebar').classList.add('expanded');
  document.getElementById('main-content').classList.add('shifted');
}
function collapseSidebar() {
  document.getElementById('sidebar').classList.remove('expanded');
  document.getElementById('main-content').classList.remove('shifted');
}
document.getElementById('sidebar').addEventListener('mouseleave', collapseSidebar);

const ctx = document.getElementById('weeklySalesChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($labels); ?>,
    datasets: [{
      label: 'Weekly Sales',
      data: <?= json_encode($weeklySalesData); ?>,
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      borderColor: 'rgba(75, 192, 192, 1)',
      borderWidth: 1
    }]
  },
  options: {
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
