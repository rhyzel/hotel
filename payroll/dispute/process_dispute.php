<?php
include '../db.php';

if (!isset($_POST['id'], $_POST['action'])) {
    header('Location: salary_dispute.php');
    exit;
}

$id = (int)$_POST['id'];
$action = $_POST['action'];

if ($action === 'approve') {
    $status = 'approved';
} elseif ($action === 'reject') {
    $status = 'rejected';
} else {
    header('Location: salary_dispute.php');
    exit;
}

$stmt = $conn->prepare("UPDATE salary_dispute SET status = ? WHERE id = ?");
$stmt->bind_param('si', $status, $id);
$stmt->execute();

header('Location: salary_dispute.php');
exit;
?>
