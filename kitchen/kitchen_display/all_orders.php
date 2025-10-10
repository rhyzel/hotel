<?php
require_once(__DIR__ . '/../utils/db.php');

$search_table = $_GET['table_number'] ?? '';
$search_guest = $_GET['guest_name'] ?? '';

$conditions = ["ko.order_type IN ('Restaurant','Room Service')"];
$params = [];

if ($search_table) {
    $conditions[] = "(
        (ko.order_type = 'Restaurant' AND ko.table_number = :table_number) OR
        (ko.order_type = 'Room Service' AND ko.room_number = :table_number)
    )";
    $params[':table_number'] = $search_table;
}
if ($search_guest) {
    $conditions[] = "ko.guest_name LIKE :guest_name";
    $params[':guest_name'] = "%$search_guest%";
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

$orderBy = $search_guest 
    ? "ORDER BY ko.guest_name ASC, ko.created_at DESC"
    : "ORDER BY ko.created_at DESC";

$query = "
  SELECT 
    ko.order_id,
    ko.order_type,
    ko.table_number,
    ko.room_number,
    ko.guest_name,
    GROUP_CONCAT(ko.item SEPARATOR ', ') AS items,
    ko.status,
    ko.order_notes,
    SUM(ko.total_amount) AS total_amount,
    SUM(ko.estimated_time) AS estimated_time,
    MAX(ko.created_at) AS order_date
  FROM kitchen_orders ko
  $where
  GROUP BY ko.order_id, ko.order_type, ko.table_number, ko.room_number, ko.guest_name, ko.status, ko.order_notes
  $orderBy
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalQuery = "
  SELECT 
    COUNT(DISTINCT ko.order_id) AS total_orders,
    SUM(total_amount) AS total_amount
  FROM kitchen_orders ko
  $where
";
$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute($params);
$total = $totalStmt->fetch(PDO::FETCH_ASSOC);

$latestTimestamp = !empty($orders) 
    ? max(array_map(fn($o) => strtotime($o['order_date']), $orders)) 
    : 0;

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=all_orders.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Order ID', 'Table/Room', 'Guest', 'Items', 'Status', 'Total Amount', 'Estimated Time (mins)', 'order_notes', 'Order Date']);
    foreach ($orders as $order) {
        $tableRoom = $order['table_number'] ? 'T'.$order['table_number'] : 'R'.$order['room_number'];
        fputcsv($output, [
            $order['order_id'],
            $tableRoom,
            $order['guest_name'],
            $order['items'],
            ucfirst($order['status']),
            $order['total_amount'],
            $order['estimated_time'] ?? '-',
            $order['order_notes'],
            $order['order_date']
        ]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Orders</title>
<link rel="stylesheet" href="kitchen_display.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header style="margin-top:10px;">
      <h1>All Orders</h1>
      <p>Total Orders: <?= (int)$total['total_orders'] ?> | Total Amount: ₱<?= number_format((float)$total['total_amount'],2) ?></p>
    </header>

   <div class="search-container" style="display:flex; justify-content:center; gap:16px; margin:10px 0 20px 0; flex-wrap:wrap;">
  <a href="/hotel/kitchen/kitchen_display/kitchen_display.php">
    <button type="button"><i class="fas fa-arrow-left"></i> Back to Queue</button>
  </a>
  <a href="?<?= http_build_query(array_merge($_GET, ['export' => 'csv'])) ?>">
    <button type="button"><i class="fas fa-file-csv"></i> Export to CSV</button>
  </a>
  <form method="GET" class="search-form" style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
    <select name="table_number" style="background-color:#FFFFFF; color:#000000; border:1px solid #ccc; border-radius:6px; padding:8px 12px; font-weight:600;">
      <option value="">All Tables / Rooms</option>
      <?php
      $tablesRooms = $pdo->query("SELECT DISTINCT table_number, room_number FROM kitchen_orders WHERE order_type IN ('Restaurant','Room Service')")->fetchAll(PDO::FETCH_ASSOC);
      foreach ($tablesRooms as $tr):
          $display = $tr['table_number'] ? 'T'.$tr['table_number'] : 'R'.$tr['room_number'];
          $value = $tr['table_number'] ?: $tr['room_number'];
      ?>
        <option value="<?= htmlspecialchars($value) ?>" <?= ($search_table == $value ? 'selected' : '') ?>>
          <?= htmlspecialchars($display) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <input type="text" name="guest_name" placeholder="Search by guest" value="<?= htmlspecialchars($search_guest) ?>" 
           style="background-color:#FFFFFF; color:#000000; border:1px solid #ccc; border-radius:6px; padding:8px 12px; font-weight:600;">
    <button type="submit" style="background-color:#1A237E; color:#FFFFFF; border:none; border-radius:6px; padding:8px 12px; font-weight:600; cursor:pointer;">
      🔍 Search
    </button>
    <?php if ($search_table || $search_guest): ?>
      <a href="all_orders.php">
        <button type="button" style="background-color:#9E9E9E; color:#FFFFFF; border:none; border-radius:6px; padding:8px 12px; font-weight:600; cursor:pointer;">
          ❌ Clear Filter
        </button>
      </a>
    <?php endif; ?>
  </form>
</div>
 <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Table / Room</th>
          <th>Guest</th>
          <th>Items</th>
          <th>Status</th>
          <th>Total Amount</th>
          <th>Estimated Time (mins)</th>
          <th>Order Notes</th>
          <th>Order Date</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($orders): ?>
        <?php foreach ($orders as $order):
            $isNew = (strtotime($order['order_date']) >= $latestTimestamp - 60) ? 'new-order' : '';
            $tableRoom = $order['table_number'] ? 'T'.$order['table_number'] : 'R'.$order['room_number'];
        ?>
          <tr class="<?= $isNew ?>">
            <td><?= htmlspecialchars($order['order_id']) ?></td>
            <td><?= htmlspecialchars($tableRoom) ?></td>
            <td><?= htmlspecialchars($order['guest_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['items'] ?? '-') ?></td>
            <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
            <td>₱<?= number_format((float)$order['total_amount'],2) ?></td>
            <td><?= htmlspecialchars($order['estimated_time'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['order_notes'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['order_date']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" style="text-align:center;">No orders found.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
