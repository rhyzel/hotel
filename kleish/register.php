<?php
session_start();
include 'kleishdb.php'; // Include database connection

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Validate that all fields are filled
    if (!empty($_POST['name']) && (!empty($_POST['mobile_number']) || !empty($_POST['email'])) && !empty($_POST['password']) && !empty($_POST['confirm_password'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $mobile_number = mysqli_real_escape_string($conn, $_POST['mobile_number']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if either the email or mobile number is already registered
        if (!empty($mobile_number)) {
            $check_mobile_query = "SELECT * FROM users WHERE mobile_number = ?";
            if ($stmt = mysqli_prepare($conn, $check_mobile_query)) {
                mysqli_stmt_bind_param($stmt, "s", $mobile_number);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) > 0) {
                    echo "<script>alert('Mobile Number is already registered. Please use a different one.'); window.location.href='register.php';</script>";
                    exit();
                }
            }
        }

        if (!empty($email)) {
            $check_email_query = "SELECT * FROM users WHERE email = ?";
            if ($stmt = mysqli_prepare($conn, $check_email_query)) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) > 0) {
                    echo "<script>alert('Email is already registered. Please use a different one.'); window.location.href='register.php';</script>";
                    exit();
                }
            }
        }

        // Check if password and confirm password match
        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match. Please try again.'); window.location.href='register.php';</script>";
            exit();
        }

        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query to insert the new user into the database
        $query = "INSERT INTO users (name, mobile_number, email, password) VALUES (?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "ssss", $name, $mobile_number, $email, $hashed_password);

            if (!mysqli_stmt_execute($stmt)) {
                // Log the error message to a file for debugging
                error_log("Error: " . mysqli_error($conn), 3, "error_log.txt");
                echo "<script>alert('There was an error during registration. Please try again later.'); window.location.href='register.php';</script>";
                exit();
            }

            // Success message and redirect to login page
            echo "<script>alert('Registration successful! Please log in again.'); window.location.href='login.php';</script>";
            exit(); // Ensure no further code is executed after redirect
        } else {
            // If prepare fails, log the error
            error_log("Error preparing query: " . mysqli_error($conn), 3, "error_log.txt");
            echo "<script>alert('There was an error during registration. Please try again later.'); window.location.href='register.php';</script>";
            exit();
        }

        // Close the prepared statement-
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Please fill in all fields.'); window.location.href='register.php';</script>";
        exit();
    }
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register | Kleish Collection</title>
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="register.css"/>

</head>
<body>
  <div class="form-container">
    <h2 class="brand-wrapper">
      <span class="brand-part kleish">Kleish</span> 
      <span class="brand-part collection">Collection</span>
      <span class="leaf">🌿</span>
    </h2>
    <form action="register.php" method="POST">
      <input type="text" name="name" placeholder="Enter your full name" required />
      <input type="text" name="mobile_number" placeholder="Enter your mobile number (optional)" />
      <input type="email" name="email" placeholder="Enter your email (optional)" />
      <input type="password" name="password" placeholder="Create a password" required />
      <input type="password" name="confirm_password" placeholder="Confirm your password" required />
      <button type="submit" name="register">Register</button>
    </form>
    <div class="switch-link">
      Already have an account? <a href="index.php">Login</a>
    </div>
  </div>
</body>
</html>
