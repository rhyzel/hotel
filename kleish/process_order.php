<?php
session_start();
include 'kleishdb.php';

// Check if the user has a cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: kleish_pos.php");
    exit;
}

// Retrieve customer name, discount, and customer_id if set
$customer_name = isset($_SESSION['customer_name']) ? $_SESSION['customer_name'] : 'Guest';
$discount_percent = isset($_SESSION['discount']) ? $_SESSION['discount'] : 0;
$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

// If the customer is a registered user, use their ID; otherwise, consider them as a guest
if ($customer_id === null) {
    // Use NULL for guest orders (this assumes your database allows NULL for the foreign key column)
    $customer_id = null; // NULL for guest orders
}

// Calculate the total of the cart
$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['total'];
}

// Apply discount
$discount_value = $grand_total * ($discount_percent / 100);
$final_total = $grand_total - $discount_value;

/// Handle order saving in database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_order'])) {
    $order_date = date('Y-m-d H:i:s');
    $order_status = 'Pending';

    // Prepare order insert with customer_id instead of customer_name
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, customer_name, discount, total, status, order_date) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        // Bind the parameters with the possibility of NULL for customer_id
        $stmt->bind_param("issdss", $customer_id, $customer_name, $discount_percent, $final_total, $order_status, $order_date);
        if ($stmt->execute()) {
            $order_id = $stmt->insert_id;
            $stmt->close();

            // Insert each item into order_items table
            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, total) 
                                         VALUES (?, ?, ?, ?)");
            if ($item_stmt) {
                // Check product existence before insertion
                foreach ($_SESSION['cart'] as $item) {
                    $product_id = $item['product_id'];

                    // Check if the product exists in the products table
                    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE product_id = ?");
                    $check_stmt->bind_param("i", $product_id);
                    $check_stmt->execute();
                    $check_stmt->bind_result($count);
                    $check_stmt->fetch();
                    $check_stmt->close();

                    // If product doesn't exist, stop and show an error
                    if ($count == 0) {
                        echo "Product with ID $product_id does not exist in the products table.";
                        exit;  // Exit to prevent further processing
                    }

                    // If product exists, insert it into the order_items table
                    $item_stmt->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['total']);
                    $item_stmt->execute();
                }
                $item_stmt->close();
            }

            // Clear cart
            unset($_SESSION['cart']);
            unset($_SESSION['customer_name']);
            unset($_SESSION['discount']);
            unset($_SESSION['customer_id']);

            // Redirect to receipt
            header("Location: receipt.php?order_id=$order_id");
            exit;
        } else {
            echo "Failed to execute order query: " . $stmt->error;
        }
    } else {
        echo "Failed to prepare order query: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Order - Kleish Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .cart-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 16px;
        }
        .cart-summary .total {
            font-weight: bold;
        }
        .order-btn {
            background: rgba(150, 116, 44, 0.86);
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }
        .order-btn:hover {
            background: rgb(212, 193, 83);
        }
        .back-btn {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: rgb(3, 3, 3);
            font-weight: bold;
            font-size: 16px;
        }
        .back-btn i {
            margin-right: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Order Summary - Kleish Collection</h2>

    <div class="cart-summary">
        <div><strong>Customer:</strong> <?= htmlspecialchars($customer_name) ?></div>
        <div><strong>Discount:</strong> <?= $discount_percent ?>%</div>
    </div>

    <div class="cart-summary">
        <div><strong>Subtotal:</strong> ₱<?= number_format($grand_total, 2) ?></div>
        <div><strong>Discount:</strong> -₱<?= number_format($discount_value, 2) ?></div>
    </div>

    <div class="cart-summary">
        <div><strong>Total:</strong> ₱<?= number_format($final_total, 2) ?></div>
    </div>


    <form method="POST" action="">
        <button type="submit" name="submit_order" class="order-btn"><i class="fas fa-check-circle"></i> Confirm Order</button>
    </form>

    <a href="kleish_pos.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to POS</a>
</div>

</body>
</html>
