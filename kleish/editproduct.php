<?php
session_start();
include_once 'kleishdb.php'; 


if (isset($_GET['id'])) {
    $product_id = $_GET['id'];


    $sql = "SELECT * FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();


    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
        $product_name = $_POST['product_name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];


        $price = filter_var($price, FILTER_VALIDATE_FLOAT);

        $update_sql = "UPDATE products SET product_name = ?, category_id = ?, price = ?, stock = ? WHERE product_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("siidi", $product_name, $category_id, $price, $stock, $product_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Product updated successfully!'); window.location.href='inventory.php';</script>";
        } else {
            echo "<script>alert('Error updating product.');</script>";
        }
    }
} else {
    echo "<script>alert('No product ID specified.'); window.location.href='inventory.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Product</title>
<link rel="stylesheet" href="edit_product.css">

</head>
<body>
  <div class="main-content">
    <header>
      <a href="inventory.php" class="back-btn">Back to Inventory</a>
      <h1>Edit Product</h1>
    </header>

    <form method="POST" action="editproduct.php?id=<?php echo $product_id; ?>">
      <label for="product_name">Product Name:</label>
      <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>

      <label for="category_id">Category:</label>
      <select name="category_id" id="category_id" required>
        <option value="1" <?php echo $product['category_id'] == 1 ? 'selected' : ''; ?>>Bottom</option>
        <option value="2" <?php echo $product['category_id'] == 2 ? 'selected' : ''; ?>>Top</option>
        <option value="3" <?php echo $product['category_id'] == 3 ? 'selected' : ''; ?>>Bags</option>
        <option value="4" <?php echo $product['category_id'] == 4 ? 'selected' : ''; ?>>Shoes</option>
        <option value="5" <?php echo $product['category_id'] == 5 ? 'selected' : ''; ?>>Jacket</option>
        <option value="6" <?php echo $product['category_id'] == 6 ? 'selected' : ''; ?>>Vintage</option>
        <option value="7" <?php echo $product['category_id'] == 7 ? 'selected' : ''; ?>>Other</option>
      </select>

      <label for="price">Price:</label>
      <input type="number" name="price" id="price" value="<?php echo number_format($product['price'], 2); ?>" step="0.01" min="0" required>

      <label for="stock">Stock Quantity:</label>
      <input type="number" name="stock" id="stock" value="<?php echo $product['stock']; ?>" required>

      <button type="submit" name="edit_product" class="btn">Update Product</button>
    </form>
  </div>
</body>
</html>
