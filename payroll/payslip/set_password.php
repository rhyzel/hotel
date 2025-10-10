<?php
session_start();
include '../db.php';
if (!isset($_SESSION['verified_reset']) && !isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}
$staff_id = $_SESSION['staff_id_reset'] ?? $_SESSION['staff_id'];
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $new_password)) {
        $error = "Password must be at least 8 characters, include 1 uppercase letter, 1 lowercase letter, and 1 number.";
    } else {
        $stmt = $conn->prepare("UPDATE staff SET password=? WHERE staff_id=?");
        $stmt->bind_param("ss", $new_password, $staff_id);
        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            header('Location: login.php?message=Password changed, please login again.');
            exit;
        } else {
            $error = "Failed to update password. Try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Set New Password - Hotel La Vista</title>
<link rel="stylesheet" href="login.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <img src="logo.png" alt="Hotel La Vista Logo" class="logo">
        <h2>Set Your New Password</h2>
        <?php if ($error) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
        <?php if ($success) echo '<p class="success">'.htmlspecialchars($success).'</p>'; ?>
        <form method="post">
            <div class="password-wrapper">
                <input type="password" name="new_password" placeholder="New Password" id="new_password" required>
                <span class="toggle-eye" onclick="togglePassword('new_password', this)">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
            <div class="password-wrapper">
                <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password" required>
                <span class="toggle-eye" onclick="togglePassword('confirm_password', this)">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
            <div class="button-row full-width">
                <button type="submit">Update Password</button>
            </div>
        </form>
    </div>
</div>
<script>
function togglePassword(inputId, eye) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = eye.querySelector('i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>
