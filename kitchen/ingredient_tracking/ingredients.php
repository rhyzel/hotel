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

$readyOrdersStmt = $pdo->query("SELECT order_id, item FROM kitchen_orders WHERE status = 'completed'");
$readyOrders = $readyOrdersStmt->fetchAll(PDO::FETCH_ASSOC);

$realistic_quantities = [
    'Chocolate Cake' => ['Chocolate Cake Mix' => 45, 'Cream' => 20],
    'Fruit Salad' => ['Fruits' => 15, 'Syrup' => 10],
    'Cheesecake' => ['Cheesecake Mix' => 60, 'Cream' => 20],
    'Brownies' => ['Brownie Mix' => 40, 'Chocolate' => 20],
    'Ice Cream Sundae' => ['Ice Cream' => 10, 'Toppings' => 5],
    'Iced Tea' => ['Tea' => 5, 'Sugar' => 5, 'Ice' => 50],
    'Fresh Juice' => ['Fresh Fruits' => 5, 'Water' => 10],
    'Lemonade' => ['Lemon' => 5, 'Water' => 10, 'Sugar' => 5],
    'Coffee' => ['Coffee' => 10, 'Water' => 50],
    'Chocolate Milkshake' => ['Milk' => 50, 'Chocolate Syrup' => 10, 'Ice' => 30],
];

foreach ($readyOrders as $order) {
    $items = explode(',', $order['item']);
    foreach ($items as $item_raw) {
        $item_raw = trim($item_raw);
        if (preg_match('/^(.*)\s*\(x(\d+)\)$/i', $item_raw, $matches)) {
            $recipe_name = trim($matches[1]);
            $order_qty = (int)$matches[2];
        } else {
            $recipe_name = $item_raw;
            $order_qty = 1;
        }

        $ingredientStmt = $pdo->prepare("
            SELECT ig.ingredient_name, ig.unit, ig.category, i.item_id, i.quantity_in_stock
            FROM ingredients ig
            JOIN inventory i ON i.item = ig.ingredient_name
            WHERE ig.recipe_id = (SELECT id FROM recipes WHERE recipe_name = :recipe_name)
        ");
        $ingredientStmt->execute([':recipe_name' => $recipe_name]);
        $ingredients = $ingredientStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($ingredients as $ing) {
            $base_qty = $realistic_quantities[$recipe_name][$ing['ingredient_name']] ?? 0;
            $used_qty = $base_qty * $order_qty;
            $new_stock = max($ing['quantity_in_stock'] - $used_qty, 0);
            $update = $pdo->prepare("UPDATE inventory SET quantity_in_stock = :new_stock, last_updated = NOW() WHERE item_id = :id");
            $update->execute([':new_stock' => $new_stock, ':id' => $ing['item_id']]);
            $log = $pdo->prepare("INSERT INTO ingredient_usage (item_id, used_qty, used_by, date_used) VALUES (:item_id, :used_qty, 'System', NOW())");
            $log->execute([':item_id' => $ing['item_id'], ':used_qty' => $used_qty]);
        }
    }
}

$deductedItemsAllOrders = $pdo->query("
    SELECT CONCAT(i.item, ' (', iu.used_qty, ' ', i.unit, ')') AS used_item
    FROM ingredient_usage iu
    JOIN inventory i ON i.item_id = iu.item_id
    ORDER BY iu.date_used DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_COLUMN);

$deductedParagraph = $deductedItemsAllOrders ? implode(', ', $deductedItemsAllOrders) . '.' : 'No recent deductions.';

$usageList = [];
$search_item = trim($_GET['item'] ?? '');
$where = '';
$params = [];
if ($search_item !== '') {
    $where = "WHERE i.item LIKE ? AND i.category IN (" . implode(',', array_fill(0, count($categories_to_show), '?')) . ")";
    $params = array_merge(['%'.$search_item.'%'], $categories_to_show);
} else {
    $where = "WHERE i.category IN (" . implode(',', array_fill(0, count($categories_to_show), '?')) . ")";
    $params = $categories_to_show;
}

$usageStmt = $pdo->prepare("
    SELECT i.item_id, i.item, i.category, i.quantity_in_stock, i.unit,
           MAX(iu.date_used) AS last_used
    FROM inventory i
    LEFT JOIN ingredient_usage iu ON i.item_id = iu.item_id
    $where
    GROUP BY i.item_id, i.item, i.category, i.quantity_in_stock, i.unit
    ORDER BY last_used DESC, i.item ASC
");
$usageStmt->execute($params);
$usageList = $usageStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Ingredients Tracking</title>
<link rel="stylesheet" href="ingredients.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body>
<div class="overlay">
    <div class="container">
        <header>
            <h1>Ingredients Tracking</h1>
            <p><strong>Last Deduction:</strong> <?= htmlspecialchars($deductedParagraph) ?></p>
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
                    <th>Stock Left</th>
                    <th>Last Used</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($usageList): ?>
                    <?php foreach($usageList as $usage): ?>
                        <tr>
                            <td data-label="Item Name"><?= htmlspecialchars($usage['item']) ?></td>
                            <td data-label="Category"><?= htmlspecialchars($usage['category']) ?></td>
                            <td data-label="Stock Left"><?= convert_for_display($usage['quantity_in_stock'], $usage['unit']) ?></td>
                            <td data-label="Last Used"><?= $usage['last_used'] ?? 'Never' ?></td>
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
