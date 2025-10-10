<?php
require_once(__DIR__ . '/../utils/db.php');

$successMessage = '';
$chefs = $pdo->query("SELECT staff_id, first_name, last_name FROM staff WHERE position_name LIKE '%Chef%' ORDER BY first_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT order_id, item_name, guest_name, table_number, room_number, order_type, guest_id, total_amount FROM kitchen_orders WHERE status='completed' AND (resolution IS NULL OR resolution='Refund') ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $items = $_POST['items'] ?? [];
    $complain_reason = $_POST['complain_reason'] ?? '';
    $assigned_chef = $_POST['assigned_chef'] ?? '';
    $resolution = $_POST['action_type'] ?? '';
    $order_notes = $_POST['order_notes'] ?? '';

    if($order_id && $items && $complain_reason && $assigned_chef && $resolution){
        $newOrderItems = [];
        foreach($items as $item){
            $stmtCheck = $pdo->prepare("SELECT id FROM reported_order WHERE order_id=? AND reported_item=?");
            $stmtCheck->execute([$order_id, $item]);
            if(!$stmtCheck->fetch()){
                $stmt = $pdo->prepare("INSERT INTO reported_order (order_id, reported_item, complain_reason, assigned_chef, resolution, status, reported_at) VALUES (?,?,?,?,?,?,NOW())");
                $stmt->execute([$order_id, $item, $complain_reason, $assigned_chef, $resolution, 'pending']);
                if($resolution === 'Replacement'){
                    $newOrderItems[] = $item;
                }
            }
        }

        if($resolution === 'Replacement' && !empty($newOrderItems)){
            $stmtOrder = $pdo->prepare("SELECT * FROM kitchen_orders WHERE order_id=? LIMIT 1");
            $stmtOrder->execute([$order_id]);
            $orderData = $stmtOrder->fetch(PDO::FETCH_ASSOC);
            if($orderData){
                foreach($newOrderItems as $newItem){
                    $stmtReplacement = $pdo->prepare("INSERT INTO replacement_orders (original_order_id, order_type, status, priority, table_number, room_number, assigned_chef, guest_name, guest_id, item_name, total_amount, complain_reason, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())");
                    $stmtReplacement->execute([
                        $orderData['order_id'],
                        $orderData['order_type'],
                        'pending',
                        $orderData['priority'],
                        $orderData['table_number'],
                        $orderData['room_number'],
                        $assigned_chef,
                        $orderData['guest_name'],
                        $orderData['guest_id'],
                        $newItem,
                        $orderData['total_amount'],
                        $complain_reason
                    ]);
                }
            }
        }

        if($resolution === 'Refund'){
            $stmtOrder = $pdo->prepare("SELECT * FROM kitchen_orders WHERE order_id=? LIMIT 1");
            $stmtOrder->execute([$order_id]);
            $orderData = $stmtOrder->fetch(PDO::FETCH_ASSOC);
            if($orderData){
                $stmtRefund = $pdo->prepare("INSERT INTO refunds (payment_id, invoice_id, refund_amount, refund_method, refund_reason, processed_by, status) VALUES (?,?,?,?,?,?,?)");
                $stmtRefund->execute([
                    null,
                    null,
                    $orderData['total_amount'],
                    'cash',
                    $complain_reason,
                    $assigned_chef,
                    'pending'
                ]);
            }
        }

        $stmtUpdate = $pdo->prepare("UPDATE kitchen_orders SET resolution=? WHERE order_id=?");
        $stmtUpdate->execute([$resolution, $order_id]);
        $successMessage = "Reported order has been saved successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Reported Order</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="add_order_report.css">
<style>
form { display:flex; flex-direction:column; gap:10px; margin-top:10px; }
label { font-weight:bold; }
input, select, textarea { padding:8px; border-radius:6px; border:1px solid #ccc; width:100%; box-sizing:border-box; }
textarea { resize:vertical; }
.alert { padding:10px; background:#4CAF50; color:#fff; border-radius:4px; margin-bottom:10px; }
.button-group { display:flex; gap:10px; margin-top:10px; }
button.module-btn, a.module-btn {
    flex:1;
    padding:10px;
    background:#1A237E;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    text-align:center;
    display:inline-flex;
    justify-content:center;
    align-items:center;
    font-weight:600;
    gap:5px;
    border:none;
    cursor:pointer;
    transition:0.2s;
}
button.module-btn:hover, a.module-btn:hover { background:#0d144d; }
</style>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header><h1>Add Reported Order</h1></header>

<?php if($successMessage): ?>
    <div class="alert"><?= htmlspecialchars($successMessage) ?></div>
<?php endif; ?>

<form method="POST">
  <label>Order ID</label>
  <select name="order_id" id="order_id" required>
    <option value="">Select Order</option>
    <?php foreach($orders as $o): ?>
        <option value="<?= $o['order_id'] ?>" data-items="<?= htmlspecialchars($o['item_name']) ?>"><?= htmlspecialchars($o['order_id'].' - '.$o['guest_name']) ?></option>
    <?php endforeach; ?>
  </select>

  <label>Items</label>
  <select name="items[]" id="items" multiple required style="height:120px;"></select>

  <label>Complain Reason</label>
  <select name="complain_reason" required>
    <option value="">Select Reason</option>
    <option value="Bland">Bland</option>
    <option value="Raw">Raw</option>
    <option value="Late Order">Late Order</option>
    <option value="Wrong Order">Wrong Order</option>
    <option value="Order Not Received">Order Not Received</option>
  </select>

  <label>Assign Chef</label>
  <select name="assigned_chef" required>
    <option value="">Select Chef</option>
    <?php foreach($chefs as $c): ?>
        <option value="<?= $c['staff_id'] ?>"><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?></option>
    <?php endforeach; ?>
  </select>

  <label>Resolution</label>
  <select name="action_type" required>
    <option value="">Select Resolution</option>
    <option value="Replacement">Replacement</option>
    <option value="Refund">Refund</option>
  </select>

  <div class="button-group">
    <button type="submit" class="module-btn"><i class="fas fa-save"></i> Save Report</button>
    <a href="order_reports.php" class="module-btn"><i class="fas fa-times"></i> Cancel</a>
  </div>
</form>

  </div>
</div>

<script>
document.getElementById('order_id').addEventListener('change', function(){
    let itemsSelect = document.getElementById('items');
    itemsSelect.innerHTML = '';
    let selected = this.selectedOptions[0];
    if(selected){
        let items = selected.dataset.items.split(',');
        items.forEach(function(item){
            let option = document.createElement('option');
            option.value = item.trim();
            option.textContent = item.trim();
            itemsSelect.appendChild(option);
        });
    }
});
</script>

</body>
</html>
