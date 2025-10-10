<?php
require_once(__DIR__ . '/../utils/db.php');

$reportedId = $_GET['id'] ?? null;
if (!$reportedId) {
    header("Location: reported_orders.php");
    exit;
}

$stmt = $pdo->prepare("SELECT ro.*, r.recipe_name 
                       FROM reported_order ro
                       LEFT JOIN recipes r ON ro.recipe_id = r.id
                       WHERE ro.id = ?");
$stmt->execute([$reportedId]);
$reported = $stmt->fetch(PDO::FETCH_ASSOC);

$reportedItem = $reported['recipe_name'] ?? $reported['reported_item'] ?? '';

$complaintCategories = [
    'Wrong Item',
    'Undercooked / Raw',
    'Bland / Tasteless',
    'Spoiled',
    'Late Delivery',
    'Damaged',
    'Order Not Received',
    'Other'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = $_POST['quantity'];
    $complain_reason = $_POST['complain_reason'];
    $action_type = $_POST['action_type'];

    $updateStmt = $pdo->prepare("UPDATE reported_order 
                                 SET quantity = ?, complain_reason = ?, resolution = ?
                                 WHERE id = ?");
    $updateStmt->execute([$quantity, $complain_reason, $action_type, $reportedId]);

    if ($action_type === 'refund') {
        $refundAmount = $reported['total_price'] ?? 0; 
        $refundStmt = $pdo->prepare("INSERT INTO refunds 
            (order_id, refund_amount, refund_method, refund_reason, refund_date, processed_by, status) 
            VALUES (?, ?, ?, ?, NOW(), ?, ?)");
        $refundStmt->execute([
            $reported['order_id'] ?? null,
            $refundAmount,
            'Original Payment',
            $complain_reason,
            'System',
            'Pending'
        ]);
    }

    header("Location: order_reports.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Reported Order</title>
<link rel="stylesheet" href="edit_reported_order.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header><h1>Edit Reported Order</h1></header>

<form method="POST">
  <label>Order ID</label>
  <input type="text" value="<?= htmlspecialchars($reported['order_id'] ?? '') ?>" readonly>

<label>Item</label>
<input type="text" value="<?= htmlspecialchars($reportedItem) ?>" readonly>

<label>Quantity</label>
<input type="number" name="quantity" min="1" value="<?= htmlspecialchars($reported['quantity'] ?? 1) ?>" required>

<label>Complaint Reason</label>
<select name="complain_reason" required>
<?php foreach($complaintCategories as $cat): ?>
<option value="<?= $cat ?>" <?= (isset($reported['complain_reason']) && $reported['complain_reason'] === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
<?php endforeach; ?>
</select>

<label>Resolution</label>
<select name="action_type" required>
<option value="replacement" <?= (isset($reported['resolution']) && $reported['resolution']=='replacement') ? 'selected' : '' ?>>Replacement</option>
<option value="refund" <?= (isset($reported['resolution']) && $reported['resolution']=='refund') ? 'selected' : '' ?>>Refund</option>
<option value="none" <?= (isset($reported['resolution']) && $reported['resolution']=='none') ? 'selected' : '' ?>>None</option>
</select>

  <div class="button-group">
    <button type="submit" class="module-btn"><i class="fas fa-save"></i></button>
    <a href="order_reports.php" class="module-btn"><i class="fas fa-times"></i></a>
  </div>
</form>

  </div>
</div>
</body>
</html>
