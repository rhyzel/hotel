<?php
session_start();
require_once('db.php');

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!$username || !$password) {
    header("Location: login.php?error=1");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM staff WHERE (staff_id=? OR email=?) AND password=? LIMIT 1");
$stmt->bind_param("sss", $username, $username, $password);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $_SESSION['hrm_logged_in'] = true;
    $_SESSION['hrm_user'] = $user;
    header("Location: hrm_payroll.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
