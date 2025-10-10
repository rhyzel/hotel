<?php
session_start();  // Start the session to store messages
include 'kleishdb.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = isset($_POST['employee_id']) ? trim($_POST['employee_id']) : '';
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Validate inputs
    if (empty($employee_id) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!ctype_digit($employee_id)) {
        $error = "Employee ID must be numeric.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update password in the database for the specified employee_id
        $stmt = $conn->prepare("UPDATE employee SET password = ? WHERE employee_id = ?");
        if (!$stmt) {
            die('Database error: ' . $conn->error);
        }

        $stmt->bind_param("si", $hashed_password, $employee_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Password successfully updated!";  // Store success message in session
            // Redirect to the login page or home page
            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to update password. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Change Password | Kleish Collection</title>
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="index.css"/>
</head>
<body>
  <div class="form-container">
    <h2 class="brand-wrapper">
      <span class="brand-part kleish">Kleish</span> 
      <span class="brand-part collection">Collection</span>
      <span class="leaf">🌿</span>
    </h2>

    <!-- Display error or success messages -->
    <?php if (!empty($error)): ?>
      <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['success_message'])): ?>
      <p style="color: green;"><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
      <?php unset($_SESSION['success_message']); // Clear the success message after displaying it ?>
    <?php endif; ?>

    <!-- Password reset form -->
    <form action="change-password.php" method="POST">
      <!-- Employee ID Input -->
      <div class="input-wrapper">
        <i class="fas fa-id-badge"></i>
        <input type="text" name="employee_id" placeholder="Enter Employee ID" value="<?php echo isset($_POST['employee_id']) ? htmlspecialchars($_POST['employee_id']) : ''; ?>" required />
      </div>

      <!-- New Password Input -->
      <div class="input-wrapper">
        <i class="fas fa-lock"></i>
        <input type="password" name="new_password" id="new_password" placeholder="Enter New Password" required />
        <span class="toggle-wrapper">
          <i class="far fa-eye" id="toggleNewPassword"></i>
        </span>
      </div>

      <!-- Confirm New Password Input -->
      <div class="input-wrapper">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required />
        <span class="toggle-wrapper">
          <i class="far fa-eye" id="toggleConfirmPassword"></i>
        </span>
      </div>

      <button type="submit">Reset Password</button>
    </form>
  </div>

  <!-- Password visibility toggle script -->
  <script>
    const toggleNewPassword = document.querySelector('#toggleNewPassword');
    const newPasswordField = document.querySelector('#new_password');
    const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
    const confirmPasswordField = document.querySelector('#confirm_password');

    // Toggle visibility of new password field
    toggleNewPassword.addEventListener('click', function () {
      const type = newPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      newPasswordField.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });

    // Toggle visibility of confirm password field
    toggleConfirmPassword.addEventListener('click', function () {
      const type = confirmPasswordField.getAttribute('type') === 'password' ? 'text' : 'password';
      confirmPasswordField.setAttribute('type', type);
      this.classList.toggle('fa-eye');
      this.classList.toggle('fa-eye-slash');
    });
  </script>
</body>
</html>
