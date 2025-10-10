<?php
require_once(__DIR__ . '/../utils/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    if (isset($_POST['order_type']) && $_POST['order_type'] === 'Replacement') {
        $stmt = $pdo->prepare("UPDATE reported_order SET status = :status WHERE order_id = :order_id");
        $stmt->execute([':status' => $new_status, ':order_id' => $order_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE kitchen_orders SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE order_id = :order_id");
        $stmt->execute([':status' => $new_status, ':order_id' => $order_id]);
    }
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$search_table = $_GET['table_number'] ?? '';
$search_status = $_GET['status'] ?? '';
$search_order = $_GET['order_id'] ?? '';
$search_guest = $_GET['guest_name'] ?? '';

$conditions = ["ko.order_type IN ('Restaurant','Room Service')", "LOWER(ko.status) != 'completed'"];
$params = [];

if ($search_table) {
    $conditions[] = "(
        (ko.order_type = 'Restaurant' AND ko.table_number = :table_number) OR
        (ko.order_type = 'Room Service' AND ko.room_number = :table_number)
    )";
    $params[':table_number'] = $search_table;
}
if ($search_status) {
    $conditions[] = "LOWER(ko.status) = :status";
    $params[':status'] = strtolower($search_status);
}
if ($search_order) {
    $conditions[] = "ko.order_id = :order_id";
    $params[':order_id'] = $search_order;
}
if ($search_guest) {
    $conditions[] = "ko.guest_name LIKE :guest_name";
    $params[':guest_name'] = "%$search_guest%";
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$query = "
  SELECT 
    ko.order_id,
    ko.order_type,
    ko.table_number,
    ko.room_number,
    ko.guest_name,
    ko.item AS items,
    ko.status,
    ko.priority,
    ko.total_amount,
    ko.estimated_time,
    ko.created_at AS order_date
  FROM kitchen_orders ko
  $where
  ORDER BY ko.priority ASC, ko.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$replacementQuery = "
  SELECT 
    ro.order_id,
    'Replacement' AS order_type,
    ko.table_number,
    ko.room_number,
    ko.guest_name,
    GROUP_CONCAT(ro.item SEPARATOR ', ') AS items,
    ro.status,
    0 AS priority,
    SUM(r.price) AS total_amount,
    GROUP_CONCAT(ro.action SEPARATOR ', ') AS action,
    SEC_TO_TIME(SUM(r.preparation_time*60)) AS estimated_time,
    ro.report_type
  FROM reported_order ro
  LEFT JOIN recipes r ON ro.item = r.recipe_name
  LEFT JOIN kitchen_orders ko ON ro.order_id = ko.order_id
  WHERE ro.resolution = 'Replacement' AND LOWER(ro.status) != 'completed'
  GROUP BY ro.order_id
  ORDER BY MAX(ro.reported_at) DESC
";

$repStmt = $pdo->prepare($replacementQuery);
$repStmt->execute();
$replacements = $repStmt->fetchAll(PDO::FETCH_ASSOC);

$orders = array_merge($orders, $replacements);

$total_orders = count($orders);
$total_amount = array_sum(array_map(fn($o) => (float)$o['total_amount'], $orders));

$latestTimestamp = !empty($orders) 
    ? max(array_map(fn($o) => isset($o['order_date']) ? strtotime($o['order_date']) : 0, $orders)) 
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Kitchen Order Queue</title>
<link rel="stylesheet" href="kitchen_display.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script>
  setInterval(() => { window.location.reload(); }, 5000);
</script>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header style="margin-top:10px;">
      <h1>Kitchen Display Integration</h1>
      <p>Total Orders: <?= $total_orders ?> | Total Amount: ₱<?= number_format($total_amount,2) ?></p>
    </header>

<div class="search-container" style="display:flex; justify-content:center; flex-wrap:wrap; align-items:center; gap:16px; margin:10px 0 20px 0;">
  <a href="/hotel/kitchen/kitchen.php">
    <button type="button"><i class="fas fa-arrow-left"></i> Back to Kitchen</button>
  </a>
  <a href="/hotel/kitchen/kitchen_display/all_orders.php">
    <button type="button" style="background-color:#2E7D32; color:#fff;"><i class="fas fa-list"></i> View All Orders</button>
  </a>
  <form method="GET" class="search-form" style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
    <input type="text" name="order_id" placeholder="Search by order ID" value="<?= htmlspecialchars($search_order) ?>" style="background-color:#FFFFFF; color:#000000; border:1px solid #ccc; border-radius:6px; padding:8px 12px; font-weight:600;">
    <input type="text" name="guest_name" placeholder="Search by guest" value="<?= htmlspecialchars($search_guest) ?>" style="background-color:#FFFFFF; color:#000000; border:1px solid #ccc; border-radius:6px; padding:8px 12px; font-weight:600;">
    <select name="table_number" style="background-color:#FFFFFF; color:#000000; border:1px solid #ccc; border-radius:6px; padding:8px 12px; font-weight:600;">
      <option value="">All Tables / Rooms</option>
      <?php
      $tablesRooms = $pdo->query("SELECT DISTINCT table_number, room_number FROM kitchen_orders WHERE order_type IN ('Restaurant','Room Service')")->fetchAll(PDO::FETCH_ASSOC);
      foreach ($tablesRooms as $tr):
          $display = $tr['table_number'] ? 'T'.$tr['table_number'] : ($tr['room_number'] ? 'R'.$tr['room_number'] : '-');
          $value = $tr['table_number'] ?: $tr['room_number'] ?: '';
      ?>
        <option value="<?= htmlspecialchars($value) ?>" <?= ($search_table == $value ? 'selected' : '') ?>><?= htmlspecialchars($display) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="status" style="background-color:#FFFFFF; color:#000000; border:1px solid #ccc; border-radius:6px; padding:8px 12px; font-weight:600;">
      <option value="">All Status</option>
      <option value="pending" <?= strtolower($search_status) === 'pending' ? 'selected' : '' ?>>Pending</option>
      <option value="preparing" <?= strtolower($search_status) === 'preparing' ? 'selected' : '' ?>>Preparing</option>
      <option value="ready" <?= strtolower($search_status) === 'ready' ? 'selected' : '' ?>>Ready</option>
      <option value="completed" <?= strtolower($search_status) === 'completed' ? 'selected' : '' ?>>Completed</option>
    </select>
    <button type="submit" style="background-color:#1A237E; color:#FFFFFF; border:none; border-radius:6px; padding:8px 12px; font-weight:600; cursor:pointer;">🔍 Filter</button>
  </form>
</div>

<table>
  <thead>
    <tr>
      <th>Order ID</th>
      <th>Table / Room</th>
      <th>Guest</th>
      <th>Items</th>
      <th>Priority</th>
      <th>Status</th>
      <th>Total Amount</th>
      <th>Estimated Time (mins)</th>
      <th>Action</th>
    </tr>
  </thead>
<tbody>
<?php if ($orders): ?>
    <?php foreach ($orders as $order):
        $isNew = (isset($order['order_date']) && strtotime($order['order_date']) >= $latestTimestamp - 60) ? 'new-order' : '';
        $tableRoom = $order['table_number'] ? 'T'.$order['table_number'] : ($order['room_number'] ? 'R'.$order['room_number'] : '-');
    ?>
      <tr class="<?= $isNew ?>">
        <td><?= htmlspecialchars($order['order_id']) ?></td>
        <td><?= htmlspecialchars($tableRoom ?: '-') ?></td>
        <td><?= htmlspecialchars($order['guest_name'] ?? '-') ?></td>
        <td><?= htmlspecialchars($order['items'] ?? '-') ?></td>
        <td><?= htmlspecialchars($order['priority'] ?? '-') ?></td>
        <td><?= htmlspecialchars(ucfirst($order['status'] ?? '-')) ?></td>
        <td>₱<?= number_format((float)$order['total_amount'],2) ?></td>
        <td><?= htmlspecialchars($order['estimated_time'] ?? '-') ?></td>
        <td>
          <form method="POST" style="display:flex; gap:5px;">
            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
            <input type="hidden" name="order_type" value="<?= $order['order_type'] ?>">
            <select name="status">
              <option value="pending" <?= strtolower($order['status'] ?? '')==='pending'?'selected':'' ?>>Pending</option>
              <option value="preparing" <?= strtolower($order['status'] ?? '')==='preparing'?'selected':'' ?>>Preparing</option>
              <option value="ready" <?= strtolower($order['status'] ?? '')==='ready'?'selected':'' ?>>Ready</option>
            </select>
            <button type="submit" name="update_status">Update</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
      <td colspan="9" style="text-align:center;">No orders in the queue.</td>
    </tr>
<?php endif; ?>
</tbody>
</table>

  </div>
</div>
</body>
</html>
