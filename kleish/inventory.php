<?php
session_start();
include_once 'kleishdb.php';

// Category filter logic
$category_filter = isset($_GET['category_filter']) ? intval($_GET['category_filter']) : 0;

$sql = "SELECT p.product_id, p.product_name, p.price, p.stock, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id";

if ($category_filter > 0) {
    $sql .= " WHERE p.category_id = ?";
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

if ($category_filter > 0) {
    $stmt->bind_param("i", $category_filter);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}

$products = $result->fetch_all(MYSQLI_ASSOC);

// Add product logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name']);
    $category_id = intval($_POST['category_id']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    if (empty($product_name) || $category_id <= 0 || $price <= 0 || $stock < 0) {
        echo "<div class='error-message'>Please fill all fields correctly.</div>";
    } else {
        $category_check_sql = "SELECT category_id FROM categories WHERE category_id = ?";
        $category_check_stmt = $conn->prepare($category_check_sql);
        if ($category_check_stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }

        $category_check_stmt->bind_param("i", $category_id);
        $category_check_stmt->execute();
        $category_check_result = $category_check_stmt->get_result();

        if ($category_check_result->num_rows > 0) {
            $insert_product_sql = "INSERT INTO products (product_name, category_id, price, stock) 
                                   VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_product_sql);
            if ($insert_stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }

            $insert_stmt->bind_param("siid", $product_name, $category_id, $price, $stock);

            if ($insert_stmt->execute()) {
                echo "<script>alert('✨ Product added successfully! ✨'); window.location.href='inventory.php';</script>";
            } else {
                echo "<div class='error-message'>Failed to add product: " . htmlspecialchars($insert_stmt->error) . "</div>";
            }
        } else {
            echo "<div class='error-message'>Selected category does not exist.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inventory Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="innventory.css">
</head>
<body>
<div class="main-content" id="main-content">
  <header>
    <h1>Inventory Management</h1>
  </header>
  <div class="dashboard">
    <div class="card" style="grid-column: 1 / -1;">
      <h3>Product List</h3>

      <form method="GET" action="inventory.php">
        <label for="category_filter">Filter by Category:</label>
        <select id="category_filter" name="category_filter">
          <option value="">All Categories</option>
          <option value="1" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '1') ? 'selected' : ''; ?>>Bottom</option>
          <option value="2" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '2') ? 'selected' : ''; ?>>Top</option>
          <option value="3" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '3') ? 'selected' : ''; ?>>Bags</option>
          <option value="4" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '4') ? 'selected' : ''; ?>>Shoes</option>
          <option value="5" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '5') ? 'selected' : ''; ?>>Jacket</option>
          <option value="6" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '6') ? 'selected' : ''; ?>>Vintage</option>
          <option value="7" <?php echo (isset($_GET['category_filter']) && $_GET['category_filter'] == '7') ? 'selected' : ''; ?>>Other</option>
        </select>
        <button type="submit" class="btn">Filter</button>
      </form>

      <button id="add-product-btn" class="btn" style="margin-top: 20px;">Add New Product</button>

 
      <div id="add-product-form" style="display: none; margin-top: 20px; padding: 20px; background-color: #fff; border: 1px solid #ccc; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); max-width: 400px; margin: 0 auto;">
          <form method="POST" action="inventory.php">
              <label for="product_name">Product Name:</label>
              <input type="text" name="product_name" id="product_name" required>

              <label for="category_id">Category:</label>
              <select name="category_id" id="category_id" required>
                  <?php
                  $category_sql = "SELECT * FROM categories";
                  $category_result = $conn->query($category_sql);
                  while ($category = $category_result->fetch_assoc()) {
                      echo '<option value="' . $category['category_id'] . '">' . $category['category_name'] . '</option>';
                  }
                  ?>
              </select>

              <label for="price">Price:</label>
              <input type="number" name="price" id="price" required>

              <label for="stock">Stock Quantity:</label>
              <input type="number" name="stock" id="stock" required>

              <button type="submit" name="add_product" class="btn">Add Product</button>
          </form>
          <button id="close-form-btn" class="btn" style="margin-top: 10px;">Close</button>
      </div>

      <table style="width:100%;border-collapse:collapse;">
        <thead>
          <tr style="background:#28a745;color:white;">
            <th style="padding:10px;">Product Name</th>
            <th style="padding:10px;">Category</th>
            <th style="padding:10px;">Stock</th>
            <th style="padding:10px;">Price</th>
            <th style="padding:10px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $row): ?>
            <tr style="border-bottom:1px solid #ccc;">
              <td style="padding:10px;"><?php echo htmlspecialchars($row['product_name']); ?></td>
              <td style="padding:10px;"><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
              <td style="padding:10px;"><?php echo $row['stock']; ?></td>
              <td style="padding:10px;">₱<?php echo number_format($row['price'], 2); ?></td>
              <td style="padding:10px;">
                <a class="btn" href="editproduct.php?id=<?php echo htmlspecialchars($row['product_id']); ?>">Edit</a>
                <a class="btn" href="deleteproduct.php?id=<?php echo htmlspecialchars($row['product_id']); ?>" onclick="return confirm('Delete this product?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" style="text-align:center;padding:10px;">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  const addProductBtn = document.getElementById('add-product-btn');
  const addProductForm = document.getElementById('add-product-form');
  const closeFormBtn = document.getElementById('close-form-btn');
  const overlay = document.createElement('div');
  overlay.id = 'overlay'; // Create an overlay for the background
  document.body.appendChild(overlay);

  // When the "Add New Product" button is clicked, open the form and overlay
  addProductBtn.addEventListener('click', function() {
    addProductForm.style.display = 'block';
    overlay.style.display = 'block';
  });

  // When the "Close" button is clicked, close the form and overlay
  closeFormBtn.addEventListener('click', function() {
    addProductForm.style.display = 'none';
    overlay.style.display = 'none';
  });

  // When the overlay is clicked, close the form and overlay
  overlay.addEventListener('click', function() {
    addProductForm.style.display = 'none';
    overlay.style.display = 'none';
  });
</script>

</body>
</html>
