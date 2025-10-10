<?php
require_once('db.php');

$totalRevenue = $conn->query("SELECT SUM(total_amount) AS total FROM guest_billing")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

$guestRevenue = $conn->query("
    SELECT guest_name, SUM(total_amount) AS total 
    FROM guest_billing 
    GROUP BY guest_name 
    ORDER BY total DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$orderTypes = $conn->query("
    SELECT order_type, SUM(total_amount) AS total 
    FROM guest_billing 
    GROUP BY order_type
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Billing Dashboard</title>
<link rel="stylesheet" href="billing_dashboard.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="sidebar">
    <img src="logo.png" alt="Hotel Logo" class="sidebar-logo">
    <ul>
        <li><a href="billing_dashboard.php" class="active">Dashboard</a></li>
        <li><a href="billing/billing.php">Billing Records</a></li>
        <li><a href="../reservation/reservation.php">Reservations</a></li>
        <li><a href="../housekeeping/housekeeping.php">Housekeeping</a></li>
        <li><a href="../pointofsale/pos.php">POS - Restaurant</a></li>
        <li><a href="../giftstore/">Gift Store</a></li>
        <li><a href="../loungebar/">Lounge Bar</a></li>
        <li><a href="../minibar/">Mini Bar</a></li>
        <li><a href="../hr/employee_login.php">HR & Staff</a></li>
        <li><a href="../analytics/reports.php">Reports</a></li>
    </ul>
</div>


<div class="main">
    <header>
        <h1>Billing & Revenue Overview</h1>
    </header>

    <div class="cards">
        <div class="card">
            <h3>Total Revenue</h3>
            <p>₱<?= number_format($totalRevenue, 2) ?></p>
        </div>

        <div class="card">
            <h3>Top Guests</h3>
            <ul>
                <?php foreach ($guestRevenue as $guest): ?>
                    <li><?= htmlspecialchars($guest['guest_name']) ?> — ₱<?= number_format($guest['total'], 2) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="orders">
        <h2>Revenue by Order Type</h2>
        <table>
            <thead>
                <tr>
                    <th>Order Type</th>
                    <th>Total Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderTypes as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_type']) ?></td>
                        <td>₱<?= number_format($row['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
