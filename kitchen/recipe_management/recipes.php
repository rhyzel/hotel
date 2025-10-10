<?php
require '../utils/db.php';

$categories = ['Appetizer','Main Course','Dessert','Beverage','Breakfast','Lunch','Dinner'];
$search_recipe = $_GET['recipe_name'] ?? '';
$search_category = $_GET['category'] ?? '';

$query = "SELECT r.*, GROUP_CONCAT(CONCAT(i.ingredient_name,' ',i.quantity_needed,' ',i.unit) SEPARATOR '\n') as ingredients_list 
          FROM recipes r 
          LEFT JOIN ingredients i ON r.id = i.recipe_id 
          WHERE 1=1";
$params = [];
if ($search_recipe) {
    $query .= " AND r.recipe_name LIKE ?";
    $params[] = "%$search_recipe%";
}
if ($search_category) {
    $query .= " AND r.category = ?";
    $params[] = $search_category;
}
$query .= " GROUP BY r.id ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_name = trim($_POST['recipe_name']);
    $category = $_POST['category'];
    $instructions = $_POST['instructions'];
    $preparation_time = (int)$_POST['preparation_time'];
    $price = (float)$_POST['price'];
    $image_path = null;

    if (!empty($_FILES['image']['name'])) {
        $uploadDir = __DIR__ . '/../uploads/recipes/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^a-z0-9_]/', '', strtolower(str_replace(' ', '_', $recipe_name)));
        $newFileName = $safeName . '.' . $ext;
        $targetFile = $uploadDir . $newFileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image_path = $newFileName;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO recipes (recipe_name, category, instructions, preparation_time, price, image_path, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW())");
    $stmt->execute([$recipe_name, $category, $instructions, $preparation_time, $price, $image_path]);

    header("Location: view_recipes.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Recipes</title>
<link rel="stylesheet" href="/hotel/kitchen/recipe_management/recipes.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.expandable {
    cursor: pointer;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.expandable.expanded {
    white-space: pre-wrap;
}
</style>
</head>
<body>
<div class="overlay">
  <div class="container">
    <header>
      <h1>Recipe & Menu Management</h1>
      <?php
        if(!empty($recipes)) {
            $menuItems = array_map(fn($r) => $r['recipe_name'], $recipes);
            shuffle($menuItems);
            $todayMenu = array_slice($menuItems, 0, min(5, count($menuItems)));
            echo '<p>Menu for Today: ' . implode(', ', $todayMenu) . '.</p>';
        } else {
            echo '<p>Create, edit, and manage all your recipes efficiently.</p>';
        }
      ?>
    </header>

    <div class="search-container">
      <form method="GET" class="search-form">
        <a href="../kitchen.php" class="module-btn"><i class="fas fa-arrow-left"></i> Back to Kitchen</a>
        <a href="add_recipe.php" class="module-btn">Add Recipe</a>
        <input type="text" name="recipe_name" placeholder="Search recipe" value="<?= htmlspecialchars($search_recipe) ?>">
        <select name="category">
          <option value="">All Categories</option>
          <?php foreach($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= ($search_category === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">🔍 Search</button>
        <a href="../utils/import_export.php" class="module-btn">Import/Export</a>
        <a href="../activities/roulette.php" class="module-btn roulette-btn">Roulette Inspiration</a>
      </form>
    </div>

    <table>
      <thead>
        <tr>
          <th>Image</th>
          <th>Recipe Name</th>
          <th>Category</th>
          <th>Ingredients</th>
          <th>Instructions</th>
          <th>Price</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if(empty($recipes)): ?>
        <tr>
          <td colspan="7" style="text-align:center;">No recipes found.</td>
        </tr>
      <?php else: ?>
        <?php foreach($recipes as $r): 
          $id = (int)$r['id'];
          $price = number_format($r['price'] ?? 0, 2);
          $ingredient_text = $r['ingredients_list'] ?? '';
          $imagePath = !empty($r['image_path']) ? "../uploads/recipes/" . htmlspecialchars($r['image_path']) : "../uploads/recipes/default.png";
        ?>
        <tr>
          <td>
            <?php if (!empty($r['image_path'])): ?>
              <a href="<?= $imagePath ?>" target="_blank">View Image</a>
            <?php else: ?>
              No Image
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($r['recipe_name']) ?></td>
          <td><?= htmlspecialchars($r['category']) ?></td>
          <td>
            <div class="expandable" data-truncated="<?= nl2br(htmlspecialchars(mb_strimwidth($ingredient_text, 0, 50, '...'))) ?>" data-full="<?= nl2br(htmlspecialchars($ingredient_text)) ?>">
              <?= nl2br(htmlspecialchars(mb_strimwidth($ingredient_text, 0, 50, '...'))) ?>
            </div>
          </td>
          <td>
            <div class="expandable" data-truncated="<?= nl2br(htmlspecialchars(mb_strimwidth($r['instructions'], 0, 50, '...'))) ?>" data-full="<?= nl2br(htmlspecialchars($r['instructions'])) ?>">
              <?= nl2br(htmlspecialchars(mb_strimwidth($r['instructions'], 0, 50, '...'))) ?>
            </div>
          </td>
          <td>₱<?= $price ?></td>
          <td>
            <a href="edit_recipe.php?id=<?= $id ?>" class="edit-btn">Edit</a>
            <form method="POST" action="delete_recipe.php" style="display:inline-block; margin:0;" onsubmit="return confirm('Are you sure you want to delete this recipe?');">
              <input type="hidden" name="id" value="<?= $id ?>">
              <button type="submit" class="delete-btn">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.querySelectorAll('.expandable').forEach(el => {
    let truncated = el.dataset.truncated;
    let full = el.dataset.full;
    el.addEventListener('click', () => {
        if(el.classList.contains('expanded')) {
            el.innerHTML = truncated;
            el.classList.remove('expanded');
        } else {
            el.innerHTML = full;
            el.classList.add('expanded');
        }
    });
});
</script>
</body>
</html>
