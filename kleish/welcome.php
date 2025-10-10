<?php
session_start();

if (!isset($_SESSION['employee_id'], $_SESSION['role'], $_SESSION['full_name'])) {
    header("Location: index.php");
    exit();
}

$name = $_SESSION['full_name'];
$role = $_SESSION['role'];
$redirectPage = ($role === 'admin') ? 'dashboard.php' : 'customer_dashboard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="3;url=<?= htmlspecialchars($redirectPage) ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | Kleish Collection</title>
  <link rel="stylesheet" href="welcome.css">
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
</head>
<body>
  <div class="welcome-container">
    <h1>Welcome, <?= htmlspecialchars($name) ?>! 🎉</h1>
    <p>Redirecting to your dashboard...</p>
  </div>

  <script>
    confetti({
      particleCount: 120,
      spread: 90,
      origin: { y: 0.6 }
    });

    setTimeout(() => {
      window.location.href = <?= json_encode($redirectPage) ?>;
    }, 3000);
  </script>
</body>
</html>
