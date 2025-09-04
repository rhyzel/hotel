<?php
require 'db.php';
session_start();

$pendingPOs = $pdo->query("
    SELECT po.po_id, po.po_number, po.item_name, po.category, po.total_amount, po.status, s.supplier_name, po.order_date, po.quantity
    FROM purchase_orders po
    JOIN suppliers s ON po.supplier_id = s.supplier_id
    WHERE po.status = 'pending'
    ORDER BY po.order_date DESC, po.po_id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$staffList = $pdo->query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff ORDER BY first_name, last_name")->fetchAll(PDO::FETCH_COLUMN);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $po_number = $_POST['po_number'] ?? null;
    $condition = $_POST['condition'] ?? null;
    $quantity_received = isset($_POST['quantity_received']) ? (int) $_POST['quantity_received'] : 0;
    $inspected_by = $_POST['inspected_by'] ?? null;

    if ($po_number && $condition && $quantity_received > 0 && $inspected_by) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT * FROM purchase_orders WHERE po_number = :po_number LIMIT 1");
            $stmt->execute([':po_number' => $po_number]);
            $poData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($poData && $poData['status'] === 'pending') {
                $po_id = $poData['po_id'];
                $itemName = $poData['item_name'];
                $category = $poData['category'];
                $unitPrice = $poData['total_amount'];

                $pdo->prepare("
                    INSERT INTO inventory (item_name, category, quantity_in_stock, unit_price, inspected_by) 
                    VALUES (:item_name, :category, :quantity_in_stock, :unit_price, :inspected_by)
                ")->execute([
                    ':item_name' => $itemName,
                    ':category' => $category,
                    ':quantity_in_stock' => $quantity_received,
                    ':unit_price' => $unitPrice,
                    ':inspected_by' => $inspected_by
                ]);

                $updateCols = ["status = 'Received'", "received_date = NOW()"];
                $updateParams = [':po_number' => $po_number];

                $poCols = $pdo->query("SHOW COLUMNS FROM purchase_orders")->fetchAll(PDO::FETCH_COLUMN);
                if (in_array('received_quantity', $poCols)) {
                    $updateCols[] = "received_quantity = :received_quantity";
                    $updateParams[':received_quantity'] = $quantity_received;
                }
                if (in_array('inspected_by', $poCols)) {
                    $updateCols[] = "inspected_by = :inspected_by";
                    $updateParams[':inspected_by'] = $inspected_by;
                }

                $pdo->prepare("UPDATE purchase_orders SET " . implode(', ', $updateCols) . " WHERE po_number = :po_number")
                    ->execute($updateParams);

                $pdo->prepare("
                    INSERT INTO grn (po_id, po_number, item_name, category, quantity_received, inspected_by, condition_status, date_received)
                    VALUES (:po_id, :po_number, :item_name, :category, :quantity_received, :inspected_by, :condition_status, NOW())
                ")->execute([
                    ':po_id' => $po_id,
                    ':po_number' => $po_number,
                    ':item_name' => $itemName,
                    ':category' => $category,
                    ':quantity_received' => $quantity_received,
                    ':inspected_by' => $inspected_by,
                    ':condition_status' => $condition
                ]);
            }

            $pdo->commit();
            $_SESSION['success'] = "Goods Received Note has been saved successfully.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['success'] = "Error: " . $e->getMessage();
        }
    }

    header("Location: grn.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Goods Received Note (GRN)</title>
<link rel="stylesheet" href="grn.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header class="grn-header">
      <h1>Goods Received Note</h1>
      <p>Select a Purchase Order to inspect and receive.</p>
    </header>

    <?php if (!empty($_SESSION['success'])): ?>
      <div class="success-message"><?= htmlspecialchars($_SESSION['success']) ?></div>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="search-row">
      <input type="search" id="po-search" placeholder="Search PO number, supplier, item..." class="search-input" />
    </div>

    <form method="POST" class="grn-form">
        <label class="label">Select Purchase Order</label>
        <select name="po_number" required class="po-select" id="po-select" size="6">
            <?php foreach($pendingPOs as $po): ?>
                <option value="<?= htmlspecialchars($po['po_number']) ?>"
                        data-poid="<?= htmlspecialchars($po['po_id']) ?>"
                        data-supplier="<?= htmlspecialchars($po['supplier_name']) ?>"
                        data-item="<?= htmlspecialchars($po['item_name']) ?>"
                        data-category="<?= htmlspecialchars($po['category']) ?>"
                        data-quantity="<?= htmlspecialchars($po['quantity'] ?? 1) ?>">
                    <?= htmlspecialchars($po['po_number']) ?> | <?= htmlspecialchars($po['supplier_name']) ?> | <?= htmlspecialchars($po['category']) ?> | <?= htmlspecialchars($po['item_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="po-details" id="po-details" aria-hidden="true"></div>

        <label class="label">Condition</label>
        <select name="condition" required class="input">
            <option value="">Select Condition</option>
            <option value="Good">Good</option>
            <option value="Damaged">Damaged</option>
            <option value="Expired">Expired</option>
        </select>

        <label class="label">Quantity Received</label>
        <input type="number" name="quantity_received" min="1" required class="input" id="quantity_received" placeholder="Quantity Received">

        <label class="label">Inspected By</label>
        <select name="inspected_by" required class="input" id="inspected_by">
            <option value="">Select Staff</option>
            <?php foreach ($staffList as $staff): ?>
                <option value="<?= htmlspecialchars($staff) ?>"><?= htmlspecialchars($staff) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-buttons">
            <button type="submit" class="btn submit"><i class="fas fa-check"></i> Receive</button>
            <a href="inventory.php" class="cancel-link"><button type="button" class="btn cancel"><i class="fas fa-times"></i> Cancel</button></a>
        </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const success = document.querySelector('.success-message');
  if (success) { setTimeout(()=> success.classList.add('hide'), 3600); setTimeout(()=> success.remove(), 4200); }

  const search = document.getElementById('po-search');
  const select = document.getElementById('po-select');
  const details = document.getElementById('po-details');

  const originalOptions = Array.from(select.options).map(opt => ({
    value: opt.value,
    poid: opt.dataset.poid || '',
    text: opt.textContent || opt.innerText,
    supplier: opt.dataset.supplier || '',
    item: opt.dataset.item || '',
    category: opt.dataset.category || '',
    quantity: opt.dataset.quantity || 1
  }));

  function buildOptions(list) {
    select.innerHTML = '';
    list.forEach(item => {
      const o = document.createElement('option');
      o.value = item.value;
      o.textContent = item.text;
      o.dataset.poid = item.poid;
      o.dataset.supplier = item.supplier;
      o.dataset.item = item.item;
      o.dataset.category = item.category;
      o.dataset.quantity = item.quantity;
      select.appendChild(o);
    });
    if (select.options.length) select.selectedIndex = 0;
    updateDetails();
  }

  function updateDetails() {
    const opt = select.options[select.selectedIndex];
    if (!opt) { details.innerHTML=''; details.setAttribute('aria-hidden','true'); return; }
    details.innerHTML = '<strong>Supplier:</strong> ' + esc(opt.dataset.supplier) +
                        '<br><strong>Category:</strong> ' + esc(opt.dataset.category) +
                        '<br><strong>Item:</strong> ' + esc(opt.dataset.item);
    details.removeAttribute('aria-hidden');
    document.getElementById('quantity_received').value = opt.dataset.quantity;
  }

  function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

  buildOptions(originalOptions);

  select.addEventListener('change', updateDetails);

  search.addEventListener('input', function(){
    const q = this.value.trim().toLowerCase();
    if (!q) { buildOptions(originalOptions); return; }
    const filtered = originalOptions.filter(item => {
      return item.text.toLowerCase().includes(q) ||
             item.supplier.toLowerCase().includes(q) ||
             item.item.toLowerCase().includes(q) ||
             item.category.toLowerCase().includes(q);
    });
    buildOptions(filtered);
  });
});
</script>
</body>
</html>
