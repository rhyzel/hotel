<?php
require_once(__DIR__ . '/../utils/db.php');

$dateFrom = $_GET['from_date'] ?? '';
$dateTo = $_GET['to_date'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM reported_order WHERE 1=1";
$params = [];

if ($dateFrom) {
    $query .= " AND reported_at >= ?";
    $params[] = $dateFrom;
}
if ($dateTo) {
    $query .= " AND reported_at <= ?";
    $params[] = $dateTo;
}
if ($search) {
    $query .= " AND (order_id LIKE ? OR item LIKE ? OR complain_reason LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY reported_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$reportedOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reported Orders</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="order_reports.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Reported Orders</h1>
    </header>

    <form method="GET" class="search-form" style="justify-content:center; flex-wrap:wrap; gap:8px; margin-bottom:20px;">
      <a href="/hotel/kitchen/kitchen.php" class="module-btn"><i class="fas fa-arrow-left"></i> Back to Kitchen</a>
      <input type="date" name="from_date" value="<?= htmlspecialchars($dateFrom) ?>">
      <input type="date" name="to_date" value="<?= htmlspecialchars($dateTo) ?>">
      <input type="text" name="search" placeholder="Search Order ID, Item, Reason" value="<?= htmlspecialchars($search) ?>">
      <button type="submit" class="module-btn">Filter</button>
      <a href="order_reports.php" class="module-btn" style="background-color:#888; color:#fff;">Reset</a>
    </form>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Order ID</th>
          <th>Guest Name</th>
          <th>Item</th>
          <th>Quantity</th>
          <th>Complain Reason</th>
          <th>Resolution</th>
          <th>Status</th>
          <th>Reported At</th>
          <th>Order Type</th>
          <th>Table / Room</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($reportedOrders)): ?>
          <tr><td colspan="11" style="text-align:center;">No reported orders found.</td></tr>
        <?php else: ?>
          <?php foreach($reportedOrders as $ro): ?>
          <tr>
            <td><?= htmlspecialchars($ro['id']) ?></td>
            <td><?= htmlspecialchars($ro['order_id']) ?></td>
            <td><?= htmlspecialchars($ro['guest_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($ro['item']) ?></td>
            <td><?= htmlspecialchars($ro['quantity']) ?></td>
            <td><?= htmlspecialchars($ro['complain_reason']) ?></td>
            <td><?= htmlspecialchars($ro['resolution'] ?? '-') ?></td>
            <td><?= htmlspecialchars($ro['status']) ?></td>
            <td><?= htmlspecialchars($ro['reported_at']) ?></td>
            <td><?= htmlspecialchars($ro['order_type'] ?? '-') ?></td>
            <td>
              <?php
                $table = $ro['table_number'] ?? null;
                $room = $ro['room_number'] ?? null;
                if($table && !$room){ echo 'T'.$table; }
                elseif($table && $room){ echo 'T'.$table.' / R'.$room; }
                elseif(!$table && $room){ echo 'R'.$room; }
                else{ echo '-'; }
              ?>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

  </div>
</div>
</body>
</html>
