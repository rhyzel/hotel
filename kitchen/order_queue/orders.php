<?php 
require_once(__DIR__ . '/../utils/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE kitchen_orders SET status = :status, updated_at = NOW() WHERE order_id = :order_id");
    $stmt->execute([':status' => $new_status, ':order_id' => $order_id]);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}

$search_order = $_GET['order_id'] ?? '';
$search_guest = $_GET['guest_name'] ?? '';

$conditions = ["ko.order_type IN ('Restaurant','Room Service')", "LOWER(ko.status) != 'completed'"];
$params = [];

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
    ko.guest_name,
    ko.status,
    ko.item,
    ko.estimated_time,
    ko.table_number,
    ko.room_number,
    ko.resolution
  FROM kitchen_orders ko
  $where
  ORDER BY ko.created_at DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_orders = count($orders);
$total_amount = array_sum(array_map(fn($o) => isset($o['total_amount']) ? (float)$o['total_amount'] : 0, $orders));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Queue</title>
<link rel="stylesheet" href="orders.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header style="margin-top:10px;">
      <h1>Order Queue</h1>
      <p>Total Orders: <?= $total_orders ?></p>
    </header>

    <div class="search-container">
      <a href="/hotel/kitchen/kitchen.php">
        <button type="button"><i class="fas fa-arrow-left"></i> Back to Kitchen</button>
      </a>
      <form method="GET" class="search-form">
        <input type="text" name="order_id" placeholder="Search by order ID" value="<?= htmlspecialchars($search_order) ?>">
        <input type="text" name="guest_name" placeholder="Search by guest" value="<?= htmlspecialchars($search_guest) ?>">
        <button type="submit">🔍 Filter</button>
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Guest</th>
          <th>Items</th>
          <th>Status</th>
          <th>Estimated Time</th>
          <th>Table / Room</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if ($orders): ?>
        <?php foreach ($orders as $order):
            $location = $order['table_number'] ? 'T'.$order['table_number'] : ($order['room_number'] ? 'R'.$order['room_number'] : '-');
            $remaining_seconds = isset($order['estimated_time']) ? ((int)$order['estimated_time'] * 60) : 0;
        ?>
          <tr>
            <td><?= htmlspecialchars($order['order_id']) ?></td>
            <td><?= htmlspecialchars($order['guest_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($order['item'] ?? '-') ?></td>
            <td><?= htmlspecialchars(ucfirst($order['status'])) ?></td>
            <td class="countdown" data-seconds="<?= $remaining_seconds ?>"><?= sprintf('%02d:%02d:%02d', floor($remaining_seconds/3600), floor(($remaining_seconds%3600)/60), $remaining_seconds%60) ?></td>
            <td><?= htmlspecialchars($location) ?></td>
            <td>
              <form method="POST" class="status-form">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <select name="status">
                  <option value="preparing" <?= $order['status']=='preparing'?'selected':'' ?>>Preparing</option>
                  <option value="ready" <?= $order['status']=='ready'?'selected':'' ?>>Ready</option>
                  <option value="completed" <?= $order['status']=='completed'?'selected':'' ?>>Completed</option>
                </select>
                <button type="submit" name="update_status"><i class="fas fa-check"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" style="text-align:center;">No orders in the queue.</td>
        </tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
function startCountdowns() {
  document.querySelectorAll('.countdown').forEach(td => {
    let seconds = parseInt(td.dataset.seconds);
    const interval = setInterval(() => {
      if (seconds <= 0) { 
        td.textContent = '00:00:00'; 
        clearInterval(interval); 
      } else {
        const hrs = Math.floor(seconds / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        td.textContent = `${String(hrs).padStart(2,'0')}:${String(mins).padStart(2,'0')}:${String(secs).padStart(2,'0')}`;
        seconds--;
      }
    }, 1000);
  });
}
window.onload = startCountdowns;
</script>
</body>
</html>
