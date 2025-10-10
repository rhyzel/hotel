<?php
session_start();

if (isset($_POST['clear_cart'])) {
    // Clear the cart by emptying the session cart
    unset($_SESSION['cart']);
    echo 'Cart cleared successfully';
} else {
    echo 'No action taken';
}
?>
