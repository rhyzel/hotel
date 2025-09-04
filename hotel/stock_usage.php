<?php 
require 'db.php';

if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("SELECT item_id, used_qty FROM stock_usage WHERE usage_id = :id");
    $stmt->execute([':id' => $delete_id]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($log) {
        $pdo->prepare("UPDATE inventory 
                       SET quantity_in_stock = quantity_in_stock + :used_qty 
                       WHERE item_id = :item_id")
            ->execute([':used_qty' => $log['used_qty'], ':item_id' => $log['item_id']]);
    }
    $pdo->prepare("DELETE FROM stock_usage WHERE usage_id = :id")->execute([':id' => $delete_id]);
    header("Location: stock_usage.php");
    exit;
}

$search_item = $_POST['item_name'] ?? '';
$search_user = $_POST['used_by'] ?? '';

$where = [];
$params = [];
if ($search_item) { 
    $where[] = "i.item_name LIKE :item_name"; 
    $params[':item_name'] = "%$search_item%"; 
}
if ($search_user) { 
    $where[] = "su.used_by LIKE :used_by"; 
    $params[':used_by'] = "%$search_user%"; 
}
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$usage_logs = $pdo->prepare("
    SELECT 
        su.usage_id,
        i.item_name,
        su.used_qty,
        su.used_by,
        su.date_used
    FROM stock_usage su
    JOIN inventory i ON su.item_id = i.item_id
    $where_sql
    ORDER BY su.date_used DESC
");
$usage_logs->execute($params);
$usage_logs = $usage_logs->fetchAll(PDO::FETCH_ASSOC);

$recent_deductions = array_map(fn($log) => $log['item_name'].' (Used by: '.$log['used_by'].', Qty: '.$log['used_qty'].')', $usage_logs);
$recent_text = $recent_deductions ? implode(", ", $recent_deductions) : "No items were recently deducted.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stock Usage</title>
<link rel="stylesheet" href="stock_usage.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <header>
    <h1>Stock Usage</h1>
    <p>Recently deducted items: <?= htmlspecialchars($recent_text) ?></p>
  </header>

  <div class="search-container">
    <form method="POST" class="search-form">
      <a href="inventory.php"><button type="button"><i class="fas fa-arrow-left"></i> Back to Inventory</button></a>
      <input type="text" name="item_name" placeholder="Search by Item" value="<?= htmlspecialchars($search_item) ?>">
      <input type="text" name="used_by" placeholder="Search by User" value="<?= htmlspecialchars($search_user) ?>">
      <button type="submit">🔍 Search</button>
      <a href="log_usage.php"><button type="button"><i class="fas fa-plus"></i> Log Usage</button></a>
    </form>
  </div>

  <table>
    <thead>
      <tr>
        <th>Item Name</th>
        <th>Total Quantity Used</th>
        <th>Used By</th>
        <th>Last Date Used</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($usage_logs): ?>
        <?php foreach($usage_logs as $log): ?>
        <tr>
          <td><?= htmlspecialchars($log['item_name']) ?></td>
          <td><?= $log['used_qty'] ?></td>
          <td><?= htmlspecialchars($log['used_by']) ?></td>
          <td><?= $log['date_used'] ?></td>
          <td>
            <a href="edit_usage.php?id=<?= $log['usage_id'] ?>">
              <button type="button" class="edit-btn"><i class="fas fa-edit"></i> Edit</button>
            </a>
            <a href="stock_usage.php?delete_id=<?= $log['usage_id'] ?>" onclick="return confirm('Are you sure you want to delete this log?');">
              <button type="button" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">No usage logs found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
