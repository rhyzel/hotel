<?php
require 'db.php';

$conditions = [];
$params = [];

$supplierInput = trim($_POST['supplier'] ?? '');
$itemInput = trim($_POST['item_name'] ?? '');
$categoryInput = trim($_POST['category'] ?? '');

if ($supplierInput !== '') {
    $conditions[] = "s.supplier_name LIKE :supplier";
    $params[':supplier'] = "%{$supplierInput}%";
}

if ($itemInput !== '') {
    $conditions[] = "i.item_name LIKE :item_name";
    $params[':item_name'] = "%{$itemInput}%";
}

if ($categoryInput !== '') {
    $conditions[] = "i.category LIKE :category";
    $params[':category'] = "%{$categoryInput}%";
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$query = "
SELECT po.po_id, po.po_number, i.item_name, i.category, s.supplier_name, po.order_date, COALESCE(po.total_amount,0) AS total_amount
FROM purchase_orders po
JOIN suppliers s ON po.supplier_id = s.supplier_id
JOIN inventory i ON po.item_name = i.item_name AND po.category = i.category
$where
ORDER BY po.order_date DESC, po.po_id DESC
";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$purchase_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_amount = 0.0;
foreach ($purchase_orders as $po) {
    $total_amount += (float) $po['total_amount'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Purchase Orders</title>
<link rel="stylesheet" href="purchase_orders.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
function updateTotal() {
    let total = 0;
    document.querySelectorAll('tbody tr').forEach(row => {
        const cell = row.querySelector('[data-label="Total Amount"]');
        if (!cell) return;
        const amount = parseFloat(cell.dataset.amount) || 0;
        total += amount;
    });
    document.getElementById('totalAmount').textContent = 'Total Purchased: ₱' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
}
window.addEventListener('DOMContentLoaded', updateTotal);
</script>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Purchase Orders</h1>
      <p id="totalAmount">Total Purchased: ₱<?= number_format($total_amount, 2) ?></p>
    </header>
    <div class="search-container">
      <form method="POST" class="search-form">
        <a href="inventory.php">
            <button type="button"><i class="fas fa-arrow-left"></i> Back to Inventory</button>
        </a>
        <input type="text" name="supplier" placeholder="Search by supplier" value="<?= htmlspecialchars($supplierInput) ?>">
        <input type="text" name="item_name" placeholder="Search by item" value="<?= htmlspecialchars($itemInput) ?>">
        <input type="text" name="category" placeholder="Search by category" value="<?= htmlspecialchars($categoryInput) ?>">
        <button type="submit" name="search">🔍 Search</button>
        <a href="add_purchase_order.php">
            <button type="button"><i class="fas fa-plus"></i> Add Order</button>
        </a>
      </form>
    </div>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>PO Number</th>
          <th>Item Name</th>
          <th>Category</th>
          <th>Supplier</th>
          <th>Order Date</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($purchase_orders as $po): ?>
        <tr>
          <td data-label="ID"><?= htmlspecialchars($po['po_id']) ?></td>
          <td data-label="PO Number"><?= htmlspecialchars($po['po_number']) ?></td>
          <td data-label="Item Name"><?= htmlspecialchars($po['item_name']) ?></td>
          <td data-label="Category"><?= htmlspecialchars($po['category']) ?></td>
          <td data-label="Supplier"><?= htmlspecialchars($po['supplier_name']) ?></td>
          <td data-label="Order Date"><?= htmlspecialchars($po['order_date']) ?></td>
          <td data-label="Total Amount" data-amount="<?= htmlspecialchars((float)$po['total_amount']) ?>">₱<?= number_format((float)$po['total_amount'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
