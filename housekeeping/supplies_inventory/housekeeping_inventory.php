<?php
include __DIR__ . '/../db.php';

$housekeepingCategories = [
    'Hotel Supplies',
    'Cleaning & Sanitation',
    'Utility Products',
    'Office Supplies',
    'Toiletries',
    'Laundry & Linen'
];

$placeholders = implode(',', array_fill(0, count($housekeepingCategories), '?'));
$stmt = $conn->prepare("SELECT * FROM inventory WHERE category IN ($placeholders) ORDER BY category, item ASC");
$stmt->bind_param(str_repeat('s', count($housekeepingCategories)), ...$housekeepingCategories);
$stmt->execute();
$result = $stmt->get_result();

$suppliesByCategory = [];
while($row = $result->fetch_assoc()){
    $suppliesByCategory[$row['category']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Supplies Inventory | Housekeeping</title>
  <link rel="stylesheet" href="housekeeping_inventory.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header>
  <div class="title-group">
    <h1><i class="fas fa-boxes"></i> Supplies Inventory</h1>
    <p>Track and manage housekeeping supplies efficiently.</p>
  </div>

  <div class="header-controls">
    <a href="../housekeeping.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
    <form method="GET" class="filter-form">
      <input type="text" name="search" placeholder="Search item" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      <button type="submit">Search</button>
      <button type="button" onclick="window.location.href='housekeeping_inventory.php'">Clear</button>
      <button type="button" onclick="window.location.href='request_supply.php'">Request Supply</button>
    </form>
  </div>
</header>

  <div class="container">
    <?php if (empty($suppliesByCategory)): ?>
      <p class="no-items">No housekeeping supplies found.</p>
    <?php else: ?>
      <?php foreach($suppliesByCategory as $category => $items): ?>
      <div class="category-section">
        <div class="category-title"><?= htmlspecialchars(ucfirst($category)) ?></div>
        <div class="inventory-grid">
          <?php foreach($items as $supply): ?>
          <div class="inventory-card <?= ($supply['quantity_in_stock'] <= 5 ? 'low-stock' : '') ?>">
            <div class="item-name"><?= htmlspecialchars($supply['item']) ?></div>
            <div class="item-quantity">Qty: <?= htmlspecialchars($supply['quantity_in_stock']) ?></div>
            <div class="item-unit"><?= htmlspecialchars($supply['unit']) ?></div>
            <div class="item-updated">Last Updated: <?= htmlspecialchars($supply['last_updated']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('header');
    header.addEventListener('mouseenter', () => header.classList.add('expanded'));
    header.addEventListener('mouseleave', () => header.classList.remove('expanded'));
  });
  </script>
</body>
</html>
<?php $conn->close(); ?>
