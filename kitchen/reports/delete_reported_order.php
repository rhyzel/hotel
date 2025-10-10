<?php
require_once(__DIR__ . '/../utils/db.php');

$reportedStmt = $pdo->query("SELECT ro.*, r.recipe_name FROM reported_order ro
                             LEFT JOIN recipes r ON ro.recipe_id = r.id
                             ORDER BY ro.reported_at DESC");
$reportedOrders = $reportedStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reported Orders</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="order_reports.css">
<style>
.module-btn { display:inline-block; padding:6px 10px; background:#1A237E; color:#fff; border-radius:6px; text-decoration:none; margin:2px; }
.module-btn:hover { background:#0d144d; }
</style>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Reported Orders</h1>
      <a href="add_reported_order.php" class="module-btn"><i class="fas fa-plus"></i> Add Reported Item</a>
    </header>

    <table>
      <thead>
        <tr>
          <th>Reported ID</th>
          <th>Order ID</th>
          <th>Item</th>
          <th>Quantity</th>
          <th>Complaint Reason</th>
          <th>Action</th>
          <th>Status</th>
          <th>Reported At</th>
          <th>Options</th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($reportedOrders)): ?>
          <tr><td colspan="9" style="text-align:center;">No reported items.</td></tr>
        <?php else: ?>
          <?php foreach($reportedOrders as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['id']) ?></td>
            <td><?= htmlspecialchars($r['order_id']) ?></td>
            <td><?= htmlspecialchars($r['recipe_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['quantity']) ?></td>
            <td><?= htmlspecialchars($r['complain_reason']) ?></td>
            <td><?= htmlspecialchars($r['action']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
            <td><?= date("Y-m-d H:i", strtotime($r['reported_at'])) ?></td>
            <td>
              <a href="edit_reported_order.php?id=<?= $r['id'] ?>" class="module-btn"><i class="fas fa-edit"></i> Edit</a>
              <a href="delete_reported_order.php?id=<?= $r['id'] ?>" class="module-btn" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
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
