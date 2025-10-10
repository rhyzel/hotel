<?php
session_start();
include '../db.php';
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = trim($_POST['staff_id']);
    $query = "SELECT * FROM staff WHERE staff_id=? LIMIT 1";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $staff = $result->fetch_assoc();
        if ($staff) {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['staff_id_reset'] = $staff_id;
            $_SESSION['otp_expires'] = time() + 300;
            $success = "OTP sent: $otp";
            header("Refresh:2; url=verify_otp.php");
        } else {
            $error = "Staff ID not found.";
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
<title>Forgot Password - Hotel La Vista</title>
<link rel="stylesheet" href="login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-container">
        <img src="logo.png" alt="Hotel La Vista Logo" class="logo">
        <h2>Forgot Password</h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <form method="post">
            <input type="text" name="staff_id" placeholder="Enter Staff ID" required>
            <div class="button-row">
                <button type="submit">Send OTP</button>
                <a href="login.php" class="back-btn">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
