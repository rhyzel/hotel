<?php
include '../db.php';

$selected_month = $_GET['month'] ?? '';
$search = $_GET['search'] ?? '';
$selected_position = $_GET['position'] ?? '';

$positions = [
    'Front Office Manager','Assistant Front Office Manager','Concierge','Room Attendant','Laundry Supervisor',
    'Assistant Housekeeper','Cashier','Bartender','Baker','Sous Chef','F&B Manager','Chef de Partie',
    'Demi Chef de Partie','Assistant F&B Manager','Waiter / Waitress','Restaurant Manager','Chief Engineer',
    'Assistant Engineer','Inventory Manager','Inventory Inspector'
];

$months_result = $conn->query("SELECT DISTINCT DATE_FORMAT(sp.created_at, '%Y-%m') AS month 
                               FROM staff_performance sp
                               JOIN staff s ON sp.staff_id = s.staff_id
                               ORDER BY month DESC");
$months = [];
while($m = $months_result->fetch_assoc()){
    $months[] = $m['month'];
}
if(!$selected_month){
    $current_month = date('Y-m');
    $selected_month = in_array($current_month, $months) ? $current_month : ($months[0] ?? $current_month);
}

$search_param1 = "%$search%";
$search_param2 = "%$search%";

if($selected_position !== ''){
    $query = "SELECT s.staff_id, s.first_name, s.last_name, s.position_name,
                     sp.score, sp.remarks, sp.created_at
              FROM staff s
              LEFT JOIN staff_performance sp 
              ON s.staff_id = sp.staff_id AND DATE_FORMAT(sp.created_at, '%Y-%m') = ?
              WHERE (s.first_name LIKE ? OR s.last_name LIKE ?)
              AND s.position_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $selected_month, $search_param1, $search_param2, $selected_position);
} else {
    $query = "SELECT s.staff_id, s.first_name, s.last_name, s.position_name,
                     sp.score, sp.remarks, sp.created_at
              FROM staff s
              LEFT JOIN staff_performance sp 
              ON s.staff_id = sp.staff_id AND DATE_FORMAT(sp.created_at, '%Y-%m') = ?
              WHERE (s.first_name LIKE ? OR s.last_name LIKE ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $selected_month, $search_param1, $search_param2);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Performance</title>
<link rel="stylesheet" href="../css/staff_performance.css">
</head>
<body>
<div class="container">
<h1>Staff Performance</h1>

<div class="controls">
  <a href="../employee_management/employee_management.php" class="back">Back</a>
  <form method="get">
    <input type="text" name="search" placeholder="Search employee" value="<?= htmlspecialchars($search) ?>">
    <select name="month">
      <?php foreach($months as $month_val):
          $selected = ($month_val==$selected_month) ? 'selected' : '';
      ?>
      <option value="<?= $month_val ?>" <?= $selected ?>><?= date('F Y', strtotime($month_val.'-01')) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="position">
      <option value="">All Positions</option>
      <?php foreach($positions as $name):
          $selected = ($name==$selected_position) ? 'selected' : '';
      ?>
      <option value="<?= $name ?>" <?= $selected ?>><?= $name ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Filter</button>
  </form>
</div>

<table>
  <thead>
    <tr>
      <th>Staff ID</th>
      <th>Name</th>
      <th>Position</th>
      <th>Score</th>
      <th>Remarks</th>
      <th>Date</th>
    </tr>
  </thead>
  <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($row['staff_id']) ?></td>
      <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
      <td><?= htmlspecialchars($row['position_name'] ?? 'N/A') ?></td>
      <td><?= htmlspecialchars($row['score']) ?></td>
      <td><?= htmlspecialchars($row['remarks']) ?></td>
      <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
</body>
</html>
