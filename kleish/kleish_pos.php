<?php
session_start();
include 'kleishdb.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Update customer name
    if (isset($_POST['customer_name'])) {
        $customer_name = trim($_POST['customer_name']);
        if (!empty($customer_name)) {
            $_SESSION['customer_name'] = $customer_name;
        }
    }

    // Update discount
    if (isset($_POST['discount'])) {
        $discount = (int)$_POST['discount'];
        if ($discount >= 0 && $discount <= 100) {
            $_SESSION['discount'] = $discount;
        }
    }

    // Add products to cart
    if (isset($_POST['product_id']) && is_array($_POST['product_id']) && isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['product_id'] as $index => $product_id) {
            $quantity = (int)$_POST['quantity'][$index];

            if ($quantity <= 0) continue; // Skip invalid quantities

            $product_id_safe = mysqli_real_escape_string($conn, $product_id);
            $product_query = mysqli_query($conn, "SELECT * FROM products WHERE product_id='$product_id_safe'");

            if ($product = mysqli_fetch_assoc($product_query)) {
                $name = $product['product_name'];
                $price = $product['price'];
                $available_quantity = $product['stock'];

                if ($available_quantity >= $quantity) {
                    $total = $price * $quantity;
                    $_SESSION['cart'][] = [
                        'product_id' => $product_id,
                        'name' => $name,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $total
                    ];

                    // Update the stock quantity in the database
                    $new_quantity = $available_quantity - $quantity;
                    mysqli_query($conn, "UPDATE products SET stock='$new_quantity' WHERE product_id='$product_id_safe'");
                } else {
                    echo "<script>alert('Not enough stock for $name. Available: $available_quantity'); window.location='kleish_pos.php';</script>";
                    exit;
                }
            }
        }
        header("Location: kleish_pos.php");
        exit;
    }

    // Handle setting custom price
    if (isset($_POST['custom_price'])) {
        $customPrice = floatval($_POST['custom_price']);
        if ($customPrice > 0) {
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['price'] == 0) {  // Assuming price 0 means custom price
                    $item['price'] = $customPrice;
                    $item['total'] = $customPrice * $item['quantity'];
                }
            }
            unset($item); // break reference
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid custom price']);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS - Kleish Collection</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="kleish_pos.css">
   
</head>
<body>

<h2><i class="fas fa-cash-register"></i> Point of Sale</h2>

<div class="grid-container">
    <div class="form-box">
        <form method="POST" id="multiProductForm">
            <label for="customer_name">Customer Name:</label>
            <input type="text" name="customer_name" id="customer_name"
                   value="<?= isset($_SESSION['customer_name']) ? htmlspecialchars($_SESSION['customer_name']) : '' ?>"
                   placeholder="Enter name" required>

            <label for="discount">Discount:</label>
            <select name="discount" id="discount">
                <?php
                $discount_options = [0, 5, 10, 15, 20, 25, 30];
                $selected_discount = $_SESSION['discount'] ?? 0;
                foreach ($discount_options as $d) {
                    $selected = ($d == $selected_discount) ? 'selected' : '';
                    echo "<option value='$d' $selected>$d%</option>";
                }
                ?>
            </select>
            <hr>

            <div id="productFields">
                <div class="product-row">
                    <label>Select Product:</label>
                    <select name="product_id[]" required>
                        <option value="" disabled selected>Select a product</option>
                        <?php
                        $products = mysqli_query($conn, "SELECT * FROM products");
                        while ($row = mysqli_fetch_assoc($products)) {
                            echo "<option value='{$row['product_id']}'>{$row['product_name']} - ₱{$row['price']}</option>";
                        }
                        ?>
                    </select>
                    <label>Qty:</label>
                    <input type="number" name="quantity[]" min="1" required>
                    <button type="button" class="remove-row" onclick="removeProductRow(this)">🗑️</button>
                </div>
            </div>
            <button type="button" onclick="addProductRow()">+ Add Another Product</button>
            <button type="submit"><i class="fas fa-cart-plus"></i> Add to Cart</button>
        </form>
    </div>
  
<div class="cart-box">
    <h3>Cart</h3>

    <?php if (!empty($_SESSION['cart'])): ?>
        <?php if (!empty($_SESSION['customer_name'])): ?>
            <p><strong>Customer:</strong> <?= htmlspecialchars($_SESSION['customer_name']) ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
    <?php
    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += $item['total'];
        $display_price = ($item['price'] == 0) ? 'Custom Price' : '₱' . number_format($item['price'], 2);
        echo "<tr>
            <td>{$item['name']}</td>
            <td>{$item['quantity']}</td>
            <td>{$display_price}</td>
            <td>₱" . number_format($item['total'], 2) . "</td>
        </tr>";
    }
    ?>
</tbody>

 </table>
        <p class="total">Grand Total: ₱<?= number_format($grand_total, 2) ?></p>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <!-- Payment and Action Buttons -->
    <div class="payment-actions">
        <label for="payment_method">Select Payment Method:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="" disabled selected>Select a payment method</option>
            <option value="cash">Cash</option>
            <option value="credit_card">Credit Card</option>
            <option value="mobile_wallet">Mobile Wallet</option>
        </select>

        <button type="button" class="clear-btn" onclick="clearCart()">Clear Cart</button>
        <button type="button" class="process-payment-btn" onclick="processPayment()">Process Payment</button>
    </div>
</div>

<script>

let currentExpression = '';

function appendToAmount(value) {
    currentExpression += value;
    document.getElementById('customAmount').value = currentExpression;
}

function clearAmount() {
    currentExpression = '';
    document.getElementById('customAmount').value = '';
}

function appendOperator(operator) {
    currentExpression += operator;
    document.getElementById('customAmount').value = currentExpression;
}

function calculateResult() {
    try {
        let result = eval(currentExpression);  // Evaluate the expression
        document.getElementById('customAmount').value = result;
    } catch (e) {
        alert("Invalid Expression");
        clearAmount();
    }
}

function setCustomPrice() {
    const customPrice = parseFloat(document.getElementById('customAmount').value);
    
    if (isNaN(customPrice) || customPrice <= 0) {
        alert("Please enter a valid price.");
        return;
    }

    // Send the custom price to the server to update the cart
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                alert('Custom price applied to items in the cart.');
                window.location.reload(); // Reload to reflect the new prices
            } else {
                alert('Error applying custom price.');
            }
        }
    };
    xhr.send('custom_price=' + encodeURIComponent(customPrice));
}

// Function to clear the cart
function clearCart() {
    if (confirm("Are you sure you want to clear the cart?")) {
        // Clear session cart using PHP via AJAX
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'clear_cart.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                // Redirect or reload to refresh the cart
                window.location.reload();
            }
        };
        xhr.send('clear_cart=true');
    }
}

// Function to process payment and redirect to process_order.php
function processPayment() {
    const paymentMethod = document.getElementById('payment_method').value;
    if (!paymentMethod) {
        alert("Please select a payment method.");
        return;
    }

    // Send the cart data and payment method to the server for processing
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'process_order.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            window.location.href = 'process_order.php';
        }
    };
    xhr.send('payment_method=' + encodeURIComponent(paymentMethod));
}
function addProductRow() {
    const container = document.getElementById('productFields');
    
    const productRow = document.createElement('div');
    productRow.classList.add('product-row');
    productRow.innerHTML = `
        <label>Select Product:</label>
        <select name="product_id[]" required>
            <option value="" disabled selected>Select a product</option>
            <?php
            $productOptions = '';
            $products = mysqli_query($conn, "SELECT * FROM products");
            while ($row = mysqli_fetch_assoc($products)) {
                $productOptions .= "<option value='{$row['product_id']}'>{$row['product_name']} - ₱{$row['price']}</option>";
            }
            echo $productOptions;
            ?>
        </select>
        <label>Qty:</label>
        <input type="number" name="quantity[]" min="1" required>
        <button type="button" class="remove-row" onclick="removeProductRow(this)">🗑️</button>
    `;
    container.appendChild(productRow);
}

function removeProductRow(button) {
    const row = button.closest('.product-row');
    if (row) row.remove();
}

</script>
