<?php
include '../db.php';

$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($id && in_array($action, ['approve','reject'])) {
    $status = $action === 'approve' ? 'Approved' : 'Rejected';
    $stmt = $conn->prepare("UPDATE reimbursements SET status=? WHERE id=? AND status='Pending'");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

header('Location: expense_reimbursement.php');
exit;
