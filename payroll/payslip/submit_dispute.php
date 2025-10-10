<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

include '../db.php';

$staff_id = $_SESSION['staff_id'];
$payout_date = $_POST['payout_date'] ?? '';
$dispute_amount = $_POST['dispute_amount'] ?? 0;
$discrepancy_reason = $_POST['discrepancy_reason'] ?? '';
$notes = $_POST['notes'] ?? '';
$proof_file_path = '';

if (empty($payout_date) || empty($discrepancy_reason) || $dispute_amount <= 0) {
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

    $target_dir = "../uploads/disputes/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $file_name = uniqid() . '.' . $file_ext;
    $proof_file_path = $target_dir . $file_name;

    if (!move_uploaded_file($_FILES['proof_file']['tmp_name'], $proof_file_path)) {
        header('Location: portal.php?error=Error+uploading+file');
        exit;
    }
}

$stmt = $conn->prepare("INSERT INTO salary_dispute (staff_id, payout_date, amount, reason, notes, dispute_details, status, date_filed, proof_file) VALUES (?, ?, ?, ?, ?, ?, 'pending', CURDATE(), ?)");
$dispute_details = "Payout Date: $payout_date\nAmount: $dispute_amount\nReason: $discrepancy_reason\nNotes: $notes";
$stmt->bind_param("ssdssss", $staff_id, $payout_date, $dispute_amount, $discrepancy_reason, $notes, $dispute_details, $proof_file_path);

if ($stmt->execute()) {
    header('Location: portal.php?success=Dispute+submitted+successfully');
} else {
    header('Location: portal.php?error=Failed+to+submit+dispute');
}
exit;
