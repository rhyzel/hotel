<?php
session_start();
include '../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = trim($_POST['staff_id']);
    $password = $_POST['password'];

    $query = "SELECT * FROM staff WHERE staff_id=? LIMIT 1";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();

        if ($staff && $password === $staff['password']) {
            $_SESSION['staff_id'] = $staff_id;

            if ($password === 'temp123') {
                header('Location: set_password.php');
                exit;
            }

            header('Location: portal.php');
            exit;
        } else {
            $error = "Invalid staff ID or password.";
        }
        $stmt->close();
    } else {
        $error = "Database query failed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Login - Hotel La Vista</title>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="login.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <img src="logo.png" alt="Hotel La Vista Logo" class="logo">
        <h2>Payslip Portal Login</h2>
        <?php if(!empty($error)) : ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <input type="text" name="staff_id" placeholder="Staff ID" required>
            <div class="password-wrapper">
                <input type="password" name="password" placeholder="Password" id="password" required>
                <span class="toggle-eye" onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>
            <div class="button-row two-buttons">
                <button type="submit">Login</button>
                <a href="forgot_password.php" class="nav-btn">Forgot Password</a>
            </div>
            <div class="button-row full-width">
                <a href="http://localhost/hotel/hr/payroll/payroll.php" class="back-btn">Back</a>
            </div>
        </form>
    </div>
</div>
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.toggle-eye i');
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
