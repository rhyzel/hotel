<?php
session_start();
include_once('kleishdb.php');

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employee_id = trim($_POST["employee_id"]);

    if (!empty($employee_id)) {
        $stmt = $conn->prepare("SELECT * FROM employee WHERE employee_id = ?");
        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // Generate token and expiration (valid for 1 hour)
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", time() + 3600);

            // Store token in DB
            $update = $conn->prepare("UPDATE employee SET reset_token = ?, reset_expires = ? WHERE employee_id = ?");
            $update->bind_param("sss", $token, $expires, $employee_id);
            $update->execute();

            // In real life, you'd send this link via email
            $resetLink = "http://yourdomain.com/reset-password.php?token=$token";

            $success = "Reset link has been sent! (Demo: <a href='$resetLink'>Reset Password</a>)";
        } else {
            $error = "No account found with that Employee ID.";
        }
    } else {
        $error = "Please enter your Employee ID.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | Kleish Collection</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f7f1ec, #d2b48c);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }
        input[type="text"] {
            width: 90%;
            padding: 0.8rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background: #5C4033;
            color: #fff;
            padding: 0.7rem 1.2rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .success {
            color: green;
            margin-bottom: 1rem;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php elseif ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="employee_id" placeholder="Enter your Employee ID" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p><a href="index.php">Back to Login</a></p>
    </div>
</body>
</html>
