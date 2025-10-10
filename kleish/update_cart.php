<?php
session_start();

// Ensure there's a session with cart data
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle custom price updates
if (isset($_POST['custom_price']) && is_numeric($_POST['custom_price'])) {
    $custom_price = floatval($_POST['custom_price']);
    
    // Iterate through the cart to apply custom price to items without a price
    foreach ($_SESSION['cart'] as &$item) {
        if (empty($item['price']) || $item['price'] == 0) {
            $item['price'] = $custom_price;
            $item['total'] = $custom_price * $item['quantity'];
        }
    }

    // Recalculate the grand total
    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += $item['total'];
    }

    // Return success response with updated grand total
    echo json_encode(['status' => 'success', 'grand_total' => $grand_total]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid custom price']);
}
?>
