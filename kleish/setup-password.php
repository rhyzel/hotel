<?php
include 'kleishdb.php';

$employee_id = 1001; // change this to your test user
$plain_password = "yourpassword123"; // the plain password

$hashed = password_hash($plain_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE employee SET password = ? WHERE employee_id = ?");
$stmt->bind_param("si", $hashed, $employee_id);

if ($stmt->execute()) {
    echo "Password updated and hashed successfully!";
} else {
    echo "Error updating password: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
