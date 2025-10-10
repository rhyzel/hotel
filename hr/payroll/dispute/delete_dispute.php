<?php
include '../db.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    header('Location: dispute_history.php');
    exit;
}

$check = $conn->prepare("SELECT id FROM salary_dispute WHERE id = ?");
$check->bind_param('i', $id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    header('Location: dispute_history.php');
    exit;
}

$delete = $conn->prepare("DELETE FROM salary_dispute WHERE id = ?");
$delete->bind_param('i', $id);
$delete->execute();

header('Location: dispute_history.php');
exit;
?>
