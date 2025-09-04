<?php
require 'db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { exit("Invalid ID"); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_used_qty = (int)$_POST['used_qty'];

    $stmt = $pdo->prepare("SELECT item_id, used_qty FROM stock_usage WHERE usage_id = :id");
    $stmt->execute([':id' => $id]);
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$log) { exit("Record not found"); }

    $item_id = $log['item_id'];
    $current_log_used = (int)$log['used_qty'];

    $stmt = $pdo->prepare("SELECT quantity_in_stock, used_qty FROM inventory WHERE item_id = :item_id");
    $stmt->execute([':item_id' => $item_id]);
    $inventory = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$inventory) { exit("Inventory record not found"); }

    $current_stock = (int)$inventory['quantity_in_stock'];
    $current_inventory_used = (int)$inventory['used_qty'];

    $diff = $new_used_qty - $current_log_used;
    $new_stock = $current_stock - $diff;
    $new_used_inventory = $current_inventory_used + $diff;

    if ($new_stock < 0) $new_stock = 0;
    if ($new_used_inventory < 0) $new_used_inventory = 0;

    $stmt = $pdo->prepare("UPDATE inventory 
                           SET quantity_in_stock = :stock, used_qty = :used 
                           WHERE item_id = :item_id");
    $stmt->execute([
        ':stock' => $new_stock,
        ':used' => $new_used_inventory,
        ':item_id' => $item_id
    ]);

    $stmt = $pdo->prepare("UPDATE stock_usage 
                           SET used_qty = :used_qty 
                           WHERE usage_id = :id");
    $stmt->execute([
        ':used_qty' => $new_used_qty,
        ':id' => $id
    ]);

    header("Location: stock_usage.php");
    exit;
}

$stmt = $pdo->prepare("SELECT su.usage_id, i.item_name, i.quantity_in_stock, su.used_qty, su.used_by
                       FROM stock_usage su
                       JOIN inventory i ON su.item_id = i.item_id
                       WHERE su.usage_id = :id");
$stmt->execute([':id' => $id]);
$log = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$log) { exit("Record not found"); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Stock Usage</title>
<link rel="stylesheet" href="add_supplier.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Edit Stock Usage</h1>
      <p>Update the quantity used; inventory will adjust automatically.</p>
    </header>

    <form method="POST" class="add-form">
      <p><strong>Item:</strong> <?= htmlspecialchars($log['item_name']) ?></p>
      <p><strong>Current Stock:</strong> <?= $log['quantity_in_stock'] ?></p>
      <input type="number" name="used_qty" value="<?= $log['used_qty'] ?>" min="1" required placeholder="Quantity Used">

      <div class="form-buttons">
        <button type="submit"><i class="fas fa-save"></i> Update</button>
        <a href="stock_usage.php">
          <button type="button" class="cancel-btn"><i class="fas fa-times"></i> Cancel</button>
        </a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
