<?php
include 'C:\xampp\htdocs\hotel\kitchen\utils\db.php';

function convert_for_display($quantity, $unit) {
    switch(strtolower($unit)) {
        case 'g': return $quantity >= 1000 ? ($quantity / 1000) . ' kg' : $quantity . ' g';
        case 'ml': return $quantity >= 1000 ? ($quantity / 1000) . ' L' : $quantity . ' ml';
        case 'pcs': return $quantity . ' pcs';
        default: return $quantity . ' ' . $unit;
    }
}

$categories_to_show = ['Meat','Seafood','Vegetable','Fruit','Dairy','Seasoning','Grain','Beverage','Spice','Others'];

$search_item = trim($_GET['item'] ?? '');
$where = '';
$params = [];
if ($search_item !== '') {
    $where = "WHERE category IN (" . implode(',', array_fill(0, count($categories_to_show), '?')) . ") AND item LIKE ?";
    $params = array_merge($categories_to_show, ['%'.$search_item.'%']);
} else {
    $where = "WHERE category IN (" . implode(',', array_fill(0, count($categories_to_show), '?')) . ")";
    $params = $categories_to_show;
}

$stmt = $pdo->prepare("
    SELECT item, category, quantity_used, created_at
    FROM stock_usage
    $where
    ORDER BY created_at DESC
");
$stmt->execute($params);
$usageList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ingredient Usage</title>
<link rel="stylesheet" href="ingredients.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<div class="overlay">
    <div class="container">
        <header>
           <header>
    <h1>Ingredient Usage</h1>
    <?php
    $recentStmt = $pdo->prepare("
        SELECT item, quantity_used, created_at
        FROM stock_usage
        WHERE category IN (" . implode(',', array_fill(0, count($categories_to_show), '?')) . ")
        ORDER BY created_at DESC
        LIMIT 3
    ");
    $recentStmt->execute($categories_to_show);
    $recentUsage = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    $recentText = $recentUsage ? implode(', ', array_map(fn($r) => $r['item'] . ' (' . $r['quantity_used'] . ')', $recentUsage)) . '.' : 'No recent deductions.';
    ?>
    <p><strong>Last 3 Deductions:</strong> <?= htmlspecialchars($recentText) ?></p>
</header>

        <div class="search-container">
            <div class="button-group">
                <a href="/hotel/kitchen/kitchen.php"><button type="button" class="btn"><i class="fas fa-arrow-left"></i> Back</button></a>
            </div>
            <form method="GET" class="search-form">
                <input type="text" name="item" placeholder="Search item" value="<?= htmlspecialchars($search_item) ?>">
                <button type="submit" class="btn">🔍 Search</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Ingredient Name</th>
                    <th>Category</th>
                    <th>Quantity Used</th>
                    <th>Date Used</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usageList): ?>
                    <?php foreach($usageList as $usage): ?>
                        <tr>
                            <td data-label="Item Name"><?= htmlspecialchars($usage['item']) ?></td>
                            <td data-label="Category"><?= htmlspecialchars($usage['category']) ?></td>
                            <td data-label="Quantity Used"><?= htmlspecialchars($usage['quantity_used']) ?></td>
                            <td data-label="Date Used"><?= htmlspecialchars($usage['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
