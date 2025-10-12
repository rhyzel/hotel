<?php
require_once('db.php');
$pdo = $conn;
$successMessage = '';

$categories = [
    'giftstore' => 'Gift Store',
    'minibar' => 'Mini Bar',
    'loungebar' => 'Lounge Bar'
];

$selected_cat = $_GET['category'] ?? '';
$order_type_filter = $categories[$selected_cat] ?? '';

$cashiers = $pdo->query("SELECT staff_id, first_name, last_name FROM staff WHERE position_name LIKE '%Cashier%' ORDER BY first_name ASC")->fetchAll(PDO::FETCH_ASSOC);

$orders = [];
if ($selected_cat && isset($categories[$selected_cat])) {
    $ordersRawStmt = $pdo->prepare("SELECT DISTINCT gb.order_id, gb.guest_name FROM guest_billing gb WHERE gb.order_type = ? ORDER BY gb.created_at DESC");
    $ordersRawStmt->execute([$order_type_filter]);
    $orders = $ordersRawStmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? '';
    $items = $_POST['items'] ?? [];
    $complain_reason = $_POST['complain_reason'] ?? '';
    $other_note = trim($_POST['other_note'] ?? '');
    $assigned_cashier = $_POST['assigned_cashier'] ?? '';
    $category_sent = $_POST['category'] ?? $selected_cat;
    $order_type_for_post = $categories[$category_sent] ?? $order_type_filter;
    $final_note = ($complain_reason === 'Other' && $other_note !== '') ? $other_note : $complain_reason;

    if ($order_id && $items && $final_note && $assigned_cashier) {
        $stmtOrderGB = $pdo->prepare("SELECT * FROM guest_billing WHERE order_id = ?");
        $stmtOrderGB->execute([$order_id]);
        $allItems = $stmtOrderGB->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as $item) {
            $exists = $pdo->prepare("SELECT id FROM reported_items WHERE order_id=? AND item=?");
            $exists->execute([$order_id, $item]);
            if (!$exists->fetch()) {
                $itemData = array_values(array_filter($allItems, fn($i) => $i['item'] === $item))[0] ?? null;
                if ($itemData) {
                    $stmt = $pdo->prepare("INSERT INTO reported_items (order_id, guest_id, guest_name, item, complain_reason, resolution, assigned_cashier, status, reported_at, order_type) VALUES (?,?,?,?,?,?,?,?,NOW(),?)");
                    $stmt->execute([
                        $order_id,
                        $itemData['guest_id'],
                        $itemData['guest_name'],
                        $item,
                        $final_note,
                        'Replacement',
                        $assigned_cashier,
                        'Replaced',
                        $order_type_for_post
                    ]);
                }
            }
        }
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
select, textarea, input[type=text] { width:100%; padding:8px; margin-top:4px; border:1px solid #ccc; border-radius:4px; }
.button-group { margin-top:20px; display:flex; gap:10px; }
.module-btn { padding:10px 15px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:5px; }
.module-btn.cancel { background:#6c757d; }
.alert { background:#d4edda; padding:10px; margin-bottom:10px; border-radius:4px; color:#155724; }
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
      <label>Category</label>
      <select name="category" onchange="if(this.value) location.href='?category='+this.value">
        <option value="">Select Category</option>
        <?php foreach($categories as $key=>$label): ?>
          <option value="<?= $key ?>" <?= $key === $selected_cat ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
        <?php endforeach; ?>
      </select>

      <?php if($selected_cat && isset($categories[$selected_cat])): ?>
        <label>Order ID</label>
        <select name="order_id" required>
          <option value="">Select Order</option>
          <?php foreach($orders as $o): ?>
            <option value="<?= $o['order_id'] ?>"><?= htmlspecialchars($o['order_id'].' - '.$o['guest_name']) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Items</label>
        <select name="items[]" multiple required style="height:150px;"></select>

        <label>Complain Reason</label>
        <select name="complain_reason" id="complain_reason" required>
          <option value="">Select Reason</option>
          <?php if($selected_cat === 'giftstore'): ?>
            <option value="Damaged Product">Damaged Product</option>
            <option value="Wrong Item">Wrong Item</option>
            <option value="Missing Item">Missing Item</option>
            <option value="Other">Other</option>
          <?php elseif($selected_cat === 'minibar'): ?>
            <option value="Expired Item">Expired Item</option>
            <option value="Wrong Item">Wrong Item</option>
            <option value="Missing Item">Missing Item</option>
            <option value="Other">Other</option>
          <?php else: ?>
            <option value="Bland / Tasteless">Bland / Tasteless</option>
            <option value="Undercooked / Raw">Undercooked / Raw</option>
            <option value="Late Delivery">Late Delivery</option>
            <option value="Wrong Item">Wrong Item</option>
            <option value="Order Not Received">Order Not Received</option>
            <option value="Other">Other</option>
          <?php endif; ?>
        </select>

        <textarea name="other_note" id="other_note" placeholder="Specify your reason..." style="display:none;"></textarea>

        <label>Assign Cashier</label>
        <select name="assigned_cashier" required>
          <option value="">Select Cashier</option>
          <?php foreach($cashiers as $c): ?>
            <option value="<?= $c['staff_id'] ?>"><?= htmlspecialchars($c['first_name'].' '.$c['last_name']) ?></option>
          <?php endforeach; ?>
        </select>

        <label>Resolution</label>
        <input type="text" value="Replacement" readonly>

        <div class="button-group">
          <button type="submit" class="module-btn"><i class="fas fa-save"></i> Save Report</button>
          <a href="javascript:history.back()" class="module-btn cancel"><i class="fas fa-times"></i> Cancel</a>
        </div>
      <?php endif; ?>
    </form>
  </div>
</div>
<script>
async function loadItems(orderId) {
    const itemsSelect = document.querySelector('select[name="items[]"]');
    itemsSelect.innerHTML = '';
    if (!orderId) return;
    const category = document.querySelector('select[name="category"]').value;
    const response = await fetch(`get_items_generic.php?order_id=${orderId}&category=${encodeURIComponent(category)}`);
    const data = await response.json();
    data.forEach(row => {
        const option = document.createElement('option');
        option.value = row.item;
        option.textContent = row.item;
        itemsSelect.appendChild(option);
    });
}

document.querySelector('select[name="order_id"]')?.addEventListener('change', function() {
    loadItems(this.value);
});
window.addEventListener('load', function() {
    const selectedOrder = document.querySelector('select[name="order_id"]')?.value;
    if (selectedOrder) loadItems(selectedOrder);
});
document.getElementById('complain_reason')?.addEventListener('change', function() {
    document.getElementById('other_note').style.display = this.value === 'Other' ? 'block' : 'none';
});
</script>
</body>
</html>
