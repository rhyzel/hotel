<?php 
require_once('../db.php');
$pdo = $conn;
$successMessage = '';

$chefs = $pdo->query("SELECT staff_id, first_name, last_name FROM staff WHERE position_name LIKE '%Chef%' ORDER BY first_name ASC")->fetchAll(PDO::FETCH_ASSOC);

$ordersRaw = $pdo->query("
    SELECT DISTINCT gb.order_id, gb.guest_name
    FROM guest_billing gb
    JOIN kitchen_orders ko ON gb.order_id = ko.order_id
    WHERE ko.status='completed'
    ORDER BY gb.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$orders = [];
foreach($ordersRaw as $o){
    $stmtCheck = $pdo->prepare("
        SELECT gb.* FROM guest_billing gb
        LEFT JOIN reported_order ro ON gb.order_id = ro.order_id AND gb.item = ro.item
        WHERE gb.order_id = ? AND gb.amount > gb.partial_payment AND ro.id IS NULL
    ");
    $stmtCheck->execute([$o['order_id']]);
    if($stmtCheck->rowCount() > 0){
        $orders[] = $o;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $items = $_POST['items'] ?? [];
    $complain_reason = $_POST['complain_reason'] ?? '';
    $other_note = $_POST['other_note'] ?? '';
    $assigned_chef = $_POST['assigned_chef'] ?? '';
    $resolution = $_POST['action_type'] ?? '';

    if($order_id && $items && $complain_reason && $assigned_chef && $resolution){
        $stmtOrder = $pdo->prepare("SELECT * FROM kitchen_orders WHERE order_id=? AND status='completed' LIMIT 1");
        $stmtOrder->execute([$order_id]);
        $orderData = $stmtOrder->fetch(PDO::FETCH_ASSOC);
        $final_note = $complain_reason === 'Other' ? $other_note : '';

        foreach($items as $item){
            $stmtCheck = $pdo->prepare("SELECT id FROM reported_order WHERE order_id=? AND item=?");
            $stmtCheck->execute([$order_id, $item]);
            if(!$stmtCheck->fetch()){
                $stmt = $pdo->prepare("
                    INSERT INTO reported_order 
                    (order_id, guest_id, guest_name, item, complain_reason, resolution, assigned_chef, status, reported_at, order_type, table_number, room_number) 
                    VALUES (?,?,?,?,?,?,?,?,NOW(),?,?,?)
                ");
                $stmt->execute([
                    $order_id,
                    $orderData['guest_id'] ?? null,
                    $orderData['guest_name'] ?? null,
                    $item,
                    $complain_reason === 'Other' ? $other_note : $complain_reason,
                    $resolution,
                    $assigned_chef,
                    'pending',
                    $orderData['order_type'] ?? null,
                    $orderData['order_type'] === 'Restaurant' ? $orderData['table_number'] : null,
                    $orderData['order_type'] === 'Room Service' ? $orderData['room_number'] : null
                ]);

                if($resolution === 'Replacement' && $orderData){
                    $stmtReplacement = $pdo->prepare("
                        INSERT INTO replacement_orders 
                        (original_order_id, order_type, status, table_number, room_number, assigned_chef, guest_name, guest_id, item, total_amount, complain_reason, created_at, updated_at) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())
                    ");
                    $stmtReplacement->execute([
                        $orderData['order_id'],
                        $orderData['order_type'],
                        'pending',
                        $orderData['order_type'] === 'Restaurant' ? $orderData['table_number'] : null,
                        $orderData['order_type'] === 'Room Service' ? $orderData['room_number'] : null,
                        $assigned_chef,
                        $orderData['guest_name'],
                        $orderData['guest_id'],
                        $item,
                        0,
                        $complain_reason === 'Other' ? $other_note : $complain_reason
                    ]);
                }

                if($resolution === 'Refund'){
                    $stmtItems = $pdo->prepare("SELECT * FROM guest_billing WHERE order_id=? AND payment_option != 'Refunded'");
                    $stmtItems->execute([$order_id]);
                    $allItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                    $totalRemaining = 0;
                    foreach($allItems as $i){
                        $totalRemaining += ($i['amount'] - $i['partial_payment']);
                    }
                    foreach($allItems as $itemData){
                        if($itemData['item'] === $item){
                            $refundAmount = $itemData['amount'] - $itemData['partial_payment'];
                            $newRemaining = max(0, $totalRemaining - $refundAmount);
                            $stmtUpdateOriginal = $pdo->prepare("
                                UPDATE guest_billing 
                                SET payment_option='Refunded', remaining_amount=?, remaining_total=? 
                                WHERE id=?
                            ");
                            $stmtUpdateOriginal->execute([$newRemaining, $newRemaining, $itemData['id']]);
                            $stmtRefund = $pdo->prepare("
                                INSERT INTO refunds 
                                (guest_id, guest_name, order_id, item, refund_amount, payment_method, status, created_at, updated_at) 
                                VALUES (?, ?, ?, ?, ?, 'Cash', 'Completed', NOW(), NOW())
                            ");
                            $stmtRefund->execute([
                                $itemData['guest_id'],
                                $itemData['guest_name'],
                                $order_id,
                                $item,
                                $refundAmount
                            ]);
                            $totalRemaining = $newRemaining;
                        }
                    }
                }
            }
        }

        $stmtUpdate = $pdo->prepare("UPDATE kitchen_orders SET resolution=?, complain_reason=?, assigned_chef=?, order_notes=? WHERE order_id=? AND status='completed'");
        $stmtUpdate->execute([$resolution, $complain_reason, $assigned_chef, $final_note, $order_id]);
        $successMessage = "Reported order has been saved successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Reported Order</title>
<link rel="stylesheet" href="reported_order.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; background: #f8f9fa; margin:0; padding:0; }
.overlay { padding: 20px; }
.container { max-width: 900px; margin: 0 auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
header { text-align: center; margin-bottom: 20px; }
label { display:block; margin-top: 10px; font-weight: bold; }
select, input[type=number], textarea { width:100%; padding:8px; margin-top:4px; border:1px solid #ccc; border-radius:4px; }
.button-group { margin-top:20px; display:flex; gap:10px; }
.module-btn { padding:10px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.module-btn.cancel { background:#6c757d; }
.alert { background:#d4edda; padding:10px; margin-bottom:10px; border-radius:4px; color:#155724; }
#itemAmounts { margin-top:5px; font-size:0.9em; color:#555; white-space: pre-line; display:flex; flex-direction:column; }
</style>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header><h1>Add Reported Order</h1></header>
    <?php if($successMessage): ?>
      <div class="alert"><?= htmlspecialchars($successMessage) ?></div>
    <?php endif; ?>
    <form method="POST" id="reportForm">
      <label>Order ID</label>
      <select name="order_id" id="order_id" required>
        <option value="">Select Order</option>
        <?php foreach($orders as $o): ?>
            <option value="<?= $o['order_id'] ?>"><?= htmlspecialchars($o['order_id'].' - '.$o['guest_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <label>Items</label>
      <select name="items[]" id="items" multiple required style="height:150px;"></select>
      <small id="itemAmounts">Select items to see individual calculation (Remaining - Refund = New Remaining).</small>
      <label>Complain Reason</label>
      <select name="complain_reason" id="complain_reason" required>
        <option value="">Select Reason</option>
        <option value="Bland / Tasteless">Bland / Tasteless</option>
        <option value="Undercooked / Raw">Undercooked / Raw</option>
        <option value="Late Delivery">Late Delivery</option>
        <option value="Wrong Item">Wrong Item</option>
        <option value="Order Not Received">Order Not Received</option>
        <option value="Other">Other</option>
      </select>
      <textarea name="other_note" id="other_note" placeholder="Specify your reason..." style="display:none;"></textarea>
      <label>Assign Chef</label>
      <select name="assigned_chef" required>
        <option value="">Select Chef</option>
        <?php foreach($chefs as $c): ?>
            <option value="<?= $c['staff_id'] ?>"><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <label>Resolution</label>
      <select name="action_type" id="action_type" required>
        <option value="">Select Resolution</option>
        <option value="Replacement">Replacement</option>
        <option value="Refund">Refund</option>
      </select>
      <div id="refundSection" style="display:none;">
        <label>Refund Amount</label>
        <input type="number" step="0.01" name="refund_amount" id="refund_amount" readonly required>
      </div>
      <div class="button-group" id="saveButtons">
        <button type="submit" class="module-btn"><i class="fas fa-save"></i> Save Report</button>
        <a href="restaurant_pos.php" class="module-btn cancel"><i class="fas fa-times"></i> Cancel</a>
      </div>
    </form>
  </div>
</div>
<script>
document.getElementById('order_id').addEventListener('change', async function(){
    let itemsSelect = document.getElementById('items');
    let itemAmounts = document.getElementById('itemAmounts');
    itemsSelect.innerHTML = '';
    itemAmounts.textContent = 'Select items to see individual calculation (Remaining - Refund = New Remaining).';
    let orderId = this.value;
    if(orderId){
        let response = await fetch('get_items.php?order_id=' + orderId);
        let data = await response.json();
        data.forEach(function(row){
            let remaining = parseFloat(row.remaining_amount) || 0;
            let option = document.createElement('option');
            option.value = row.item;
            option.textContent = row.item;
            option.dataset.remaining = remaining;
            itemsSelect.appendChild(option);
        });
        document.getElementById('refund_amount').value = 0;
    } else {
        document.getElementById('refund_amount').value = 0;
    }
});

document.getElementById('items').addEventListener('change', function(){
let resolution = document.getElementById('action_type').value;
if(resolution !== 'Refund') return;
let totalRemaining = 0;
Array.from(this.options).forEach(o => { totalRemaining += parseFloat(o.dataset.remaining) || 0; });
let totalRefund = 0;
let itemAmounts = document.getElementById('itemAmounts');
if(this.selectedOptions.length > 0){
let text = '';
let runningRemaining = totalRemaining;
Array.from(this.selectedOptions).forEach(opt => {
let refund = parseFloat(opt.dataset.remaining) || 0;
let newRemaining = Math.max(0, runningRemaining - refund);
totalRefund += refund;
text += `${opt.value} | Remaining: ₱${runningRemaining.toFixed(2)} - Refund: ₱${refund.toFixed(2)} = New Remaining: ₱${newRemaining.toFixed(2)}\n`;
runningRemaining = newRemaining;
});
itemAmounts.textContent = text.trim();
} else {
itemAmounts.textContent = '';
totalRefund = 0;
}
document.getElementById('refund_amount').value = totalRefund.toFixed(2);
});

document.getElementById('action_type').addEventListener('change', function(){
document.getElementById('refundSection').style.display = this.value === 'Refund' ? 'block' : 'none';
document.getElementById('items').dispatchEvent(new Event('change'));
});

document.getElementById('complain_reason').addEventListener('change', function(){
document.getElementById('other_note').style.display = this.value === 'Other' ? 'block' : 'none';
});
</script>
</body>
</html>
