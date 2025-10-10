<?php
include '../../db.php';

$staff_name = $_GET['staff_name'] ?? '';
$staff_name = htmlspecialchars($staff_name);

$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$totalAmount = 0;

if ($staff_name) {
    $query = "
        SELECT b.*, CONCAT(s.first_name, ' ', s.last_name) AS fullname
        FROM bonuses_incentives b
        JOIN staff s ON b.staff_id = s.staff_id
        WHERE CONCAT(s.first_name, ' ', s.last_name) = ?
    ";

    $params = [$staff_name];
    $types = "s";

    if ($from_date && $to_date) {
        $query .= " AND DATE(b.created_at) BETWEEN ? AND ?";
        $params[] = $from_date;
        $params[] = $to_date;
        $types .= "ss";
    }

    $query .= " ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $bonusesResult = $stmt->get_result();
    $bonuses = $bonusesResult->fetch_all(MYSQLI_ASSOC);

    foreach ($bonuses as $b) {
        $totalAmount += $b['amount'];
    }
} else {
    $bonuses = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($staff_name) ?> Bonuses and Incentives</title>
    <link rel="stylesheet" href="staff_bonuses.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="page-header">
        <h2><?= htmlspecialchars($staff_name) ?></h2>
        <p class="total-amount">Total: <?= number_format($totalAmount, 2) ?></p>
        <div class="header-controls filter-export-container">
            <a href="bonuses_incentives.php" class="nav-btn">&#8592; Back to All Bonuses</a>
            <form method="get" class="filter-export-form">
                <input type="hidden" name="staff_name" value="<?= htmlspecialchars($staff_name) ?>">
                <label>From: <input type="date" name="from_date" value="<?= $from_date ?>"></label>
                <label>To: <input type="date" name="to_date" value="<?= $to_date ?>"></label>
                <button type="submit" class="edit-btn"><i class="fas fa-filter"></i> Filter</button>
                <a href="?staff_name=<?= urlencode($staff_name) ?>&export=csv&from_date=<?= $from_date ?>&to_date=<?= $to_date ?>" class="edit-btn"><i class="fas fa-file-csv"></i> Export CSV</a>
            </form>
        </div>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($bonuses)): ?>
                    <?php foreach ($bonuses as $b): ?>
                        <tr>
                            <td><?= htmlspecialchars($b['type']) ?></td>
                            <td><?= number_format($b['amount'], 2) ?></td>
                            <td><?= date('F j, Y', strtotime($b['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">No bonuses or incentives recorded for this employee.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
