<?php
session_start();
include 'kleishdb.php'; // Database connection

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');

    if (empty($employee_id) || empty($new_password)) {
        $error = "Please fill in all fields.";
    } else {
        // Hash the new password securely
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE employee SET password = ?, first_login = 1 WHERE employee_id = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $hashed_password, $employee_id);
            if ($stmt->execute()) {
                $success = "✅ Password updated successfully for Employee ID: $employee_id.";
            } else {
                $error = "❌ Failed to update password.";
            }
            $stmt->close();
        } else {
            $error = "Database error: " . htmlspecialchars($conn->error);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password | Kleish Collection</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 2rem;
      background: #f3f3f3;
    }
    .form-container {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      border-radius: 5px;
      font-weight: bold;
    }
    .message {
      margin-top: 10px;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
    }
    .success {
      background-color: #d4edda;
      color: #155724;
    }
    .error {
      background-color: #f8d7da;
      color: #721c24;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Reset Employee Password 🔐</h2>

    <?php if (!empty($success)) echo "<div class='message success'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='message error'>$error</div>"; ?>

    <form method="POST">
      <label for="employee_id">Employee ID:</label>
      <input type="text" name="employee_id" required>

      <label for="new_password">New Password:</label>
      <input type="password" name="new_password" required>

      <button type="submit">Reset Password</button>
    </form>
  </div>
</body>
</html>
