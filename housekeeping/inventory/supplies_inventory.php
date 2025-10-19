<?php
include __DIR__ . '/../db.php';
$result = $conn->query("SELECT item, quantity_in_stock, unit FROM inventory");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Supplies Inventory | Housekeeping</title>
<link rel="stylesheet" href="../housekeeping.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <h1>Supplies Inventory</h1>
    <table>
      <thead>
        <tr>
          <th>Item</th>
          <th>Quantity</th>
          <th>Unit</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['item']) ?></td>
          <td><?= $row['quantity_in_stock'] ?></td>
          <td><?= htmlspecialchars($row['unit']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <a href="../housekeeping.php">← Back</a>
  </div>
</div>
<?php $conn->close(); ?>
</body>
</html>
