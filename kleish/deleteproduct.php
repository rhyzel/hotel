<?php
session_start();
include_once 'kleishdb.php'; // Include the DB connection

// Check if product_id is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Delete product from the database
    $delete_sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $product_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product deleted successfully.'); window.location.href='inventory.php';</script>";
    } else {
        echo "<script>alert('Error deleting product.'); window.location.href='inventory.php';</script>";
    }
} else {
    echo "<script>alert('No product ID specified.'); window.location.href='inventory.php';</script>";
}
?>
