<?php
require 'db.php';

$search_item = $_GET['item_name'] ?? '';
$search_category = $_GET['category'] ?? '';

$categories = [
  'Hotel Supplies',
  'Foods & Beverages',
  'Cleaning & Sanitation',
  'Utility Products',
  'Office Supplies',
  'Kitchen Equipment',
  'Furniture & Fixtures',
  'Laundry & Linen',
  'Others'
];

$conditions = [];
$params = [];

if ($search_item) {
    $conditions[] = "i.item_name LIKE :item_name";
    $params[':item_name'] = "%$search_item%";
}

if ($search_category) {
    $conditions[] = "i.category = :category";
    $params[':category'] = $search_category;
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$query = "
    SELECT 
        i.item_name,
        i.category,
        SUM(COALESCE(i.quantity_in_stock,0)) AS quantity_in_stock,
        SUM(COALESCE(i.used_qty,0)) AS used_qty,
        AVG(COALESCE(i.unit_price,0)) AS unit_price,
        SUM(COALESCE(i.quantity_in_stock,0) * COALESCE(i.unit_price,0)) AS total_cost,
        GROUP_CONCAT(DISTINCT NULLIF(TRIM(i.inspected_by), '') SEPARATOR ', ') AS inspected_by
    FROM inventory i
    $where
    GROUP BY i.item_name, i.category
    ORDER BY i.item_name ASC
";

$stmt = $pdo->prepare($query);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->execute();
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalQuery = "
    SELECT 
        SUM(COALESCE(quantity_in_stock,0)) AS total_quantity,
        SUM(COALESCE(used_qty,0)) AS total_used
    FROM inventory
";
$totalStmt = $pdo->query($totalQuery);
$total = $totalStmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Stock Monitoring</title>
<link rel="stylesheet" href="stock_monitoring.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
setInterval(() => {
    window.location.reload();
}, 5000);
</script>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Stock Monitoring</h1>
      <p>Total quantity in inventory: <?= (int)$total['total_quantity'] ?> | Total used: <?= (int)$total['total_used'] ?></p>
    </header>

    <div class="search-container">
      <a href="inventory.php">
        <button type="button"><i class="fas fa-arrow-left"></i> Back to Inventory</button>
      </a>
      <form method="GET" class="search-form">
        <input type="text" name="item_name" placeholder="Search by item name" value="<?= htmlspecialchars($search_item) ?>">
        <select name="category">
          <option value="">All Categories</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= ($search_category == $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">🔍 Search</button>
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <th>Item Name</th>
          <th>Category</th>
          <th>Total Quantity</th>
          <th>Total Used Quantity</th>
          <th>Unit Price (Avg)</th>
          <th>Total Cost</th>
          <th>Inspected By</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $item):
          $qty = (int)$item['quantity_in_stock'];
          $used_qty = (int)($item['used_qty'] ?? 0);
          $qtyClass = $qty <= 10 ? 'low-stock' : ($qty <= 50 ? 'avg-stock' : 'high-stock');

          $category = $item['category'] ?: 'Uncategorized';
          $rawInspected = $item['inspected_by'] ?? '';
          $inspectedArr = array_values(array_filter(array_map('trim', explode(',', $rawInspected)), fn($v)=>$v !== ''));
          $inspected = $inspectedArr ? implode(', ', $inspectedArr) : '-';
      ?>
        <tr>
          <td><?= htmlspecialchars($item['item_name']) ?></td>
          <td><?= htmlspecialchars($category) ?></td>
          <td class="<?= $qtyClass ?>"><?= $qty ?></td>
          <td><?= $used_qty ?></td>
          <td>₱<?= number_format((float)$item['unit_price'],2) ?></td>
          <td>₱<?= number_format((float)$item['total_cost'],2) ?></td>
          <td><?= htmlspecialchars($inspected) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
