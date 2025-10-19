<?php
// inventory_reports.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli("localhost", "root", "", "hotel");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get selected category from URL parameter
$selected_category = $_GET['category'] ?? 'All';

// Fetch category list for dropdown
$categories = [];
$result_cat = $conn->query("SELECT DISTINCT category FROM inventory ORDER BY category ASC");
while ($row = $result_cat->fetch_assoc()) {
    $categories[] = $row['category'];
}

// Query per category totals
$sql = "SELECT 
            category,
            SUM(quantity_in_stock) AS total_stock,
            SUM(used_qty) AS total_used,
            SUM(COALESCE(wasted_qty,0)) AS total_wasted
        FROM inventory";

if ($selected_category !== 'All') {
    $sql .= " WHERE category = '" . $conn->real_escape_string($selected_category) . "'";
}

$sql .= " GROUP BY category ORDER BY category ASC";
$result = $conn->query($sql);

$labels = [];
$used_data = [];
$wasted_data = [];
$remaining_data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['category'];
    $used = (float)$row['total_used'];
    $wasted = (float)$row['total_wasted'];
    $remaining = max($row['total_stock'] - ($used + $wasted), 0);

    $used_data[] = $used;
    $wasted_data[] = $wasted;
    $remaining_data[] = $remaining;
}

// Fetch inventory data for table
$sql_items = "SELECT * FROM inventory";
if ($selected_category !== 'All') {
    $sql_items .= " WHERE category = '" . $conn->real_escape_string($selected_category) . "'";
}
$result_items = $conn->query($sql_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inventory Reports - Hotel La Vista</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="inventory_reports.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>

<body>
<div class="overlay">
    <div style="text-align: left; margin-bottom: 20px;">
        <a href="../reports.php" class="back-link">⬅ Back to Reports</a>
    </div>

    <header>
        <h1>📦 Inventory Reports</h1>
        <p>Monitor and analyze your hotel’s inventory performance</p>
    </header>

    <!-- Tabs -->
    <div class="tabs">
        <button class="tab-btn active" onclick="openTab(event, 'stockMonitoring')">🧮 Stock Monitoring</button>
        <button class="tab-btn" onclick="openTab(event, 'usageTracking')">📊 Usage Tracking</button>
        <button class="tab-btn" onclick="openTab(event, 'costAnalysis')">💰 Cost Analysis</button>
        <button class="tab-btn" onclick="openTab(event, 'forecasting')">🔮 Forecasting</button>
    </div>

    <!-- STOCK MONITORING TAB -->
    <div id="stockMonitoring" class="tab-content active">
        <div class="card">
            <div class="filters" style="text-align:center; margin-bottom:20px;">
                <form method="GET">
                    <label for="category" style="color:white; font-weight:600;">Category:</label>
                    <select name="category" id="category" onchange="this.form.submit()">
                        <option value="All" <?= ($selected_category == 'All') ? 'selected' : '' ?>>All</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= ($selected_category == $cat) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <div class="chart-container" style="height:600px; width:100%;">
                <canvas id="horizontalStackedChart"></canvas>
            </div>
        </div>

        <div class="card">
            <h2 style="margin-bottom:15px;">📋 Inventory Details</h2>
            <table>
                <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity in Stock</th>
                        <th>Used Quantity</th>
                        <th>Wasted Quantity</th>
                        <th>Remaining</th>
                        <th>Unit</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result_items->num_rows > 0): ?>
                    <?php while ($row = $result_items->fetch_assoc()):
                        $wasted = $row['wasted_qty'] ?? 0;
                        $remaining = max($row['quantity_in_stock'] - ($row['used_qty'] + $wasted), 0);
                    ?>
                        <tr>
                            <td><?= $row['item_id'] ?></td>
                            <td><?= htmlspecialchars($row['item']) ?></td>
                            <td><?= htmlspecialchars($row['category']) ?></td>
                            <td><?= $row['quantity_in_stock'] ?></td>
                            <td><?= $row['used_qty'] ?></td>
                            <td><?= $wasted ?></td>
                            <td><?= $remaining ?></td>
                            <td><?= htmlspecialchars($row['unit']) ?></td>
                            <td><?= $row['last_updated'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" style="text-align:center;">No data found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="usageTracking" class="tab-content">
        <div class="card"><h2>📊 Usage Tracking</h2><p>Coming soon...</p></div>
    </div>

    <div id="costAnalysis" class="tab-content">
        <div class="card"><h2>💰 Cost Analysis</h2><p>Coming soon...</p></div>
    </div>

    <div id="forecasting" class="tab-content">
        <div class="card"><h2>🔮 Forecasting</h2><p>Coming soon...</p></div>
    </div>

    <button id="backToTop" onclick="scrollToTop()">Back to Top</button>
</div>

<script>
// ---------- TAB FUNCTION ----------
function openTab(evt, tabName) {
    document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(tb => tb.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    evt.currentTarget.classList.add('active');
}

// ---------- HORIZONTAL STACKED BAR CHART ----------
const ctx = document.getElementById('horizontalStackedChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [
            {
                label: 'Used',
                data: <?= json_encode($used_data) ?>,
                backgroundColor: '#e69419'
            },
            {
                label: 'Wasted',
                data: <?= json_encode($wasted_data) ?>,
                backgroundColor: '#dc3545'
            },
            {
                label: 'Remaining',
                data: <?= json_encode($remaining_data) ?>,
                backgroundColor: '#17a2b8'
            }
        ]
    },
    options: {
        indexAxis: 'y', // Horizontal
        responsive: true,
        scales: {
            x: {
                stacked: true,
                ticks: { color: 'white', font: { size: 12 } },
                grid: { color: '#444' }
            },
            y: {
                stacked: true,
                ticks: { color: 'white', font: { size: 12 } },
                grid: { color: '#333' }
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: 'white', font: { size: 13 } }
            },
            title: {
                display: true,
                text: 'Inventory Category Breakdown (Used • Wasted • Remaining)',
                color: 'white',
                font: { size: 18, weight: 'bold' }
            },
            datalabels: {
                color: 'black',
                font: { weight: 'bold', size: 11 },
                formatter: (value) => value > 0 ? value : '',
                anchor: 'center',
                align: 'center'
            }
        }
    },
    plugins: [ChartDataLabels]
});

// ---------- BACK TO TOP ----------
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

</body>
</html>
