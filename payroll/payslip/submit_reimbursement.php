<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

include '../db.php';

$staff_id = $_SESSION['staff_id'];
$amount = $_POST['amount'] ?? 0;
$reason = $_POST['reason'] ?? '';
$notes = $_POST['notes'] ?? '';
$proof_file_path = '';

if ($amount <= 0 || empty($reason)) {
    header('Location: portal.php?error=Please+fill+all+required+fields');
    exit;
}

if (isset($_FILES['proof_file']) && $_FILES['proof_file']['error'] === 0) {
    $allowed_ext = ['jpg', 'jpeg'];
    $file_ext = strtolower(pathinfo($_FILES['proof_file']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, $allowed_ext)) {
        header('Location: portal.php?error=Only+JPG+files+are+allowed');
        exit;
    }
    $target_dir = "../uploads/reimbursements/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $file_name = uniqid() . '.' . $file_ext;
    $proof_file_path = $target_dir . $file_name;
    if (!move_uploaded_file($_FILES['proof_file']['tmp_name'], $proof_file_path)) {
        header('Location: portal.php?error=Error+uploading+file');
        exit;
    }
}

$stmt = $conn->prepare("INSERT INTO reimbursements (staff_id, amount, reason, notes, proof_file, status, submitted_at) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("sdsss", $staff_id, $amount, $reason, $notes, $proof_file_path);

if ($stmt->execute()) {
    header("Location: portal.php?success=Reimbursement+submitted+successfully");
} else {
    header("Location: portal.php?error=Failed+to+submit+reimbursement");
}
exit;
