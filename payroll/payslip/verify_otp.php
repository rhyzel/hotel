<?php
session_start();
include '../db.php';
$error = '';
if (!isset($_SESSION['otp']) || !isset($_SESSION['staff_id_reset'])) {
    header('Location: forgot_password.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp']);
    if (time() > $_SESSION['otp_expires']) {
        $error = "OTP expired. Please try again.";
        unset($_SESSION['otp']);
    } elseif ($entered_otp == $_SESSION['otp']) {
        $_SESSION['verified_reset'] = true;
        header('Location: set_password.php');
        exit;
    } else {
        $error = "Invalid OTP. Try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verify OTP - Hotel La Vista</title>
<link rel="stylesheet" href="login.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <img src="logo.png" alt="Hotel La Vista Logo" class="logo">
        <h2>Enter OTP</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <div class="button-row">
                <button type="submit">Verify OTP</button>
                <a href="forgot_password.php" class="back-btn">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
