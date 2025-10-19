<?php
session_start();
if (!empty($_SESSION['hrm_logged_in'])) {
    header('Location: hrm_payroll.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HRM Payroll Login</title>
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="login-overlay">
    <div class="login-card">
      <h2>Payroll Login</h2>
      <?php if (!empty($_GET['error'])): ?>
        <div class="error">Invalid email or password</div>
      <?php endif; ?>
      <form action="auth.php" method="post">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Log In</button>
      </form>
    </div>
  </div>
</body>
</html>
