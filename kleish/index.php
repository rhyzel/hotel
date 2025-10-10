<?php
session_start();
include_once('kleishdb.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $employee_id = trim($_POST["employee_id"]);
    $password = $_POST["password"];

    if (!empty($employee_id) && !empty($password)) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE employee_id = ?");
        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        if ($user && $password === $user["password"]) {
            $_SESSION["employee_id"] = $user["employee_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            header("Location: welcome.php");
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } else {
        $error = "Please enter both employee ID and password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login | Kleish Collection</title>
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

    <?php if (!empty($error)): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="index.php" method="POST">
      <div class="input-wrapper">
        <i class="fas fa-id-badge"></i>
        <input type="text" name="employee_id" placeholder="Enter your Employee ID" value="<?php echo isset($_POST['employee_id']) ? htmlspecialchars($_POST['employee_id']) : ''; ?>" required />
      </div>

      <div class="input-wrapper">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" placeholder="Enter your password" required />
        <span class="toggle-wrapper">
          <i class="far fa-eye" id="togglePassword"></i>
        </span>
      </div>

      <button type="submit">Login</button>
    </form>

    <div class="switch-link">
      Forgot your password? <a href="change-password.php">Reset Password</a>
    </div>

    <script>
      const togglePassword = document.querySelector('#togglePassword');
      const passwordField = document.querySelector('#password');

      togglePassword.addEventListener('click', function () {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
      });
    </script>
  </div>
</body>
</html>
