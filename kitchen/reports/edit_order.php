<?php
require_once(__DIR__ . '/../utils/db.php');

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    header("Location: order_reports.php");
    exit;
}

$stmt = $pdo->prepare("SELECT ko.*, 
    s.staff_id, s.first_name, s.last_name
    FROM kitchen_orders ko
    LEFT JOIN staff s ON s.staff_id = ko.assigned_chef
    WHERE ko.order_id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: order_reports.php");
    exit;
}

$itemsStmt = $pdo->prepare("SELECT oi.recipe_id, r.recipe_name, oi.quantity
                            FROM order_items oi
                            JOIN recipes r ON oi.recipe_id = r.id
                            WHERE oi.order_id = ?");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

$complaintCategories = [
    'Wrong Item',
    'Undercooked / Raw',
    'Bland / Tasteless',
    'Spoiled',
    'Late Delivery',
    'Damaged',
    'Other'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = $_POST['recipe_id'];
    $quantity = $_POST['quantity'];
    $complain_reason = $_POST['complain_reason'];
    $action = $_POST['action'];

    $stmt = $pdo->prepare("INSERT INTO reported_order (order_id, recipe_id, quantity, complain_reason, action, status, reported_at)
                           VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$orderId, $recipe_id, $quantity, $complain_reason, $action]);
    header("Location: reported_orders.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Reported Item</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.overlay {
    background-color: rgba(245, 245, 220, 0.85);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 20px 0;
}
.container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    background-color: rgba(26, 35, 126, 0.95);
    padding: 20px 25px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
header h1 {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    color: #F5F5DC;
    margin: 0 0 16px 0;
}
form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
form label {
    font-weight: 600;
    color: #F5F5DC;
}
form input[type="text"],
form select,
form textarea {
    padding: 8px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    background-color: #fff;
    color: #000;
    font-weight: 600;
}
form textarea {
    resize: vertical;
    min-height: 70px;
}
.button-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.button-group button,
.button-group a {
    flex: 1;
    text-align: center;
    padding: 10px 0;
    font-size: 14px;
    font-weight: 600;
    border-radius: 6px;
    text-decoration: none;
    cursor: pointer;
}
.button-group button,
.button-group a {
    background-color: #1A237E;
    color: #fff;
    border: none;
}
.button-group button:hover,
.button-group a:hover {
    background-color: #0d144d;
}
@media (max-width: 900px) {
    .container {
        width: 90%;
        padding: 15px 20px;
    }
    form input[type="text"],
    form select,
    form textarea,
    .button-group button,
    .button-group a {
        width: 100%;
    }
}
</style>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Add Reported Item for Order #<?= htmlspecialchars($order['order_id']) ?></h1>
    </header>

    <form method="POST">
      <label>Guest Name</label>
      <input type="text" value="<?= htmlspecialchars($order['guest_name']) ?>" readonly>

      <label>Table / Room</label>
      <input type="text" value="<?= $order['table_number'] ? 'T'.$order['table_number'] : 'R'.$order['room_id'] ?>" readonly>

      <label>Items</label>
      <textarea readonly><?php foreach($items as $i){ echo $i['recipe_name'] . " (Qty: ".$i['quantity'].")\n"; } ?></textarea>

      <label>Select Item</label>
      <select name="recipe_id" required>
        <option value="">-- Select Item --</option>
        <?php foreach($items as $i): ?>
          <option value="<?= $i['recipe_id'] ?>"><?= htmlspecialchars($i['recipe_name']) ?> (Ordered: <?= $i['quantity'] ?>)</option>
        <?php endforeach; ?>
      </select>

      <label>Quantity</label>
      <input type="number" name="quantity" min="1" value="1" required>

      <label>Complaint Reason</label>
      <select name="complain_reason" required>
        <option value="">-- Select Reason --</option>
        <?php foreach($complaintCategories as $cat): ?>
          <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Action</label>
      <select name="action" required>
        <option value="">-- Select Action --</option>
        <option value="replacement">Replacement</option>
        <option value="refund">Refund</option>
        <option value="none">None</option>
      </select>

      <div class="button-group">
        <button type="submit"><i class="fas fa-plus"></i> Add Reported Item</button>
        <a href="reported_orders.php">Back to Reported Orders</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
