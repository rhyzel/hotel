<?php
require 'db.php';

$suppliers = $pdo->query("
    SELECT supplier_id, supplier_name 
    FROM suppliers 
    WHERE is_active = 1 
    ORDER BY supplier_name
")->fetchAll(PDO::FETCH_ASSOC);

$lastPo = $pdo->query("
    SELECT po_number 
    FROM purchase_orders 
    ORDER BY po_id DESC LIMIT 1
")->fetchColumn();

$po_number = $lastPo && preg_match('/PO-(\d+)/', $lastPo, $matches) 
    ? 'PO-' . ($matches[1] + 1) 
    : 'PO-1001';

$message = '';
$selectedSupplier = (int)($_POST['supplier_id'] ?? 0);
$posted_unit_price = isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0;
$posted_quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$posted_category = $_POST['category'] ?? '';
$total_amount = $posted_quantity * $posted_unit_price;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_po'])) {
    $supplier_id = (int)($_POST['supplier_id'] ?? 0);
    $order_date = $_POST['order_date'] ?? null;
    $item_name = $_POST['item_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    $unit_price = isset($_POST['unit_price']) ? (float)$_POST['unit_price'] : 0.0;
    $total_amount = $quantity * $unit_price;

    if ($supplier_id && $order_date && $item_name && $category && $quantity > 0 && $total_amount > 0) {
        $stmt = $pdo->prepare("
            INSERT INTO purchase_orders 
            (po_number, supplier_id, order_date, status, total_amount, item_name, category, quantity) 
            VALUES (:po_number, :supplier_id, :order_date, 'Pending', :total_amount, :item_name, :category, :quantity)
        ");
        $stmt->execute([
            ':po_number' => $po_number,
            ':supplier_id' => $supplier_id,
            ':order_date' => $order_date,
            ':total_amount' => $total_amount,
            ':item_name' => $item_name,
            ':category' => $category,
            ':quantity' => $quantity
        ]);

        $message = "Purchase order saved successfully!";
        $lastPo = $pdo->query("SELECT po_number FROM purchase_orders ORDER BY po_id DESC LIMIT 1")->fetchColumn();
        $po_number = $lastPo && preg_match('/PO-(\d+)/', $lastPo, $matches) ? 'PO-' . ($matches[1] + 1) : $po_number;
        $posted_quantity = 1;
        $posted_unit_price = 0;
        $total_amount = 0;
    } else {
        $message = "All fields are required.";
        $selectedSupplier = $supplier_id;
        $posted_unit_price = $unit_price;
        $posted_quantity = $quantity;
        $total_amount = $quantity * $unit_price;
        $posted_category = $category;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Purchase Order</title>
<link rel="stylesheet" href="add_purchase_order.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Add Purchase Order</h1>
      <p>Choose a supplier to show only items they sell.</p>
    </header>

    <?php if($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="add-form" id="po-form">
      <label>PO Number</label>
      <input type="text" name="po_number" value="<?= htmlspecialchars($po_number) ?>" readonly>

      <label>Supplier</label>
      <select name="supplier_id" id="supplier_id" required>
        <option value="">Select Supplier</option>
        <?php foreach($suppliers as $s): ?>
          <option value="<?= (int)$s['supplier_id'] ?>" <?= $selectedSupplier === (int)$s['supplier_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($s['supplier_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Order Date</label>
      <input type="date" name="order_date" required value="<?= htmlspecialchars($_POST['order_date'] ?? date('Y-m-d')) ?>">

      <label>Item</label>
      <input type="text" name="item_name" id="item_name" required value="<?= htmlspecialchars($_POST['item_name'] ?? '') ?>">

      <label>Category</label>
      <select name="category" id="category" required>
        <option value="">Select Category</option>
        <option value="Hotel Supplies" <?= $posted_category === 'Hotel Supplies' ? 'selected' : '' ?>>Hotel Supplies</option>
        <option value="Foods & Beverages" <?= $posted_category === 'Foods & Beverages' ? 'selected' : '' ?>>Foods & Beverages</option>
        <option value="Cleaning & Sanitation" <?= $posted_category === 'Cleaning & Sanitation' ? 'selected' : '' ?>>Cleaning & Sanitation</option>
        <option value="Utility Products" <?= $posted_category === 'Utility Products' ? 'selected' : '' ?>>Utility Products</option>
        <option value="Office Supplies" <?= $posted_category === 'Office Supplies' ? 'selected' : '' ?>>Office Supplies</option>
        <option value="Kitchen Equipment" <?= $posted_category === 'Kitchen Equipment' ? 'selected' : '' ?>>Kitchen Equipment</option>
        <option value="Furniture & Fixtures" <?= $posted_category === 'Furniture & Fixtures' ? 'selected' : '' ?>>Furniture & Fixtures</option>
        <option value="Laundry & Linen" <?= $posted_category === 'Laundry & Linen' ? 'selected' : '' ?>>Laundry & Linen</option>
        <option value="Others" <?= $posted_category === 'Others' ? 'selected' : '' ?>>Others</option>
      </select>

      <label>Quantity</label>
      <input type="number" name="quantity" id="quantity" min="1" value="<?= htmlspecialchars($posted_quantity) ?>" required>

      <label>Unit Price</label>
      <input type="number" step="0.01" name="unit_price" id="unit_price" required value="<?= htmlspecialchars($posted_unit_price) ?>">

      <label>Total Amount</label>
      <input type="number" name="total_amount" id="total_amount" step="0.01" readonly value="<?= htmlspecialchars($total_amount) ?>">

      <div class="form-buttons">
        <button type="submit" name="submit_po"><i class="fas fa-plus"></i> Submit</button>
        <a href="purchase_orders.php">
          <button type="button" class="cancel-btn"><i class="fas fa-times"></i> Cancel</button>
        </a>
      </div>
    </form>
  </div>
</div>

<script>
const qtyInput = document.getElementById('quantity');
const unitPriceInput = document.getElementById('unit_price');
const totalInput = document.getElementById('total_amount');

function updateTotal(){
  const q = parseFloat(qtyInput.value) || 0;
  const p = parseFloat(unitPriceInput.value) || 0;
  totalInput.value = (q * p).toFixed(2);
}

qtyInput.addEventListener('input', updateTotal);
unitPriceInput.addEventListener('input', updateTotal);
window.addEventListener('DOMContentLoaded', updateTotal);
</script>
</body>
</html>
