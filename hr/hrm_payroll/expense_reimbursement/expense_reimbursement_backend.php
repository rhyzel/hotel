<?php
include_once(__DIR__ . '/../../db_connector.php');
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status'=>'error','message'=>'POST only']); exit; }
$employee_id = $_POST['employee_id'] ?? '';
$expense_type = $_POST['expense_type'] ?? '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.0;
$description = $_POST['description'] ?? '';
if(empty($employee_id)) { echo json_encode(['status'=>'error','message'=>'employee_id required']); exit; }
if(empty($expense_type)) { echo json_encode(['status'=>'error','message'=>'expense_type required']); exit; }
if($amount <= 0) { echo json_encode(['status'=>'error','message'=>'amount must be > 0']); exit; }

// resolve employee - allow numeric ceo.id or staff.staff_id
$empIdNum = intval($employee_id);
$exists = false;
$q = "SELECT id FROM ceo WHERE id = ? LIMIT 1";
if($stmt = $conn->prepare($q)){
    $stmt->bind_param('i', $empIdNum);
    $stmt->execute(); $res = $stmt->get_result(); if($res && $res->num_rows>0){ $exists = true; }
    $stmt->close();
}
if(!$exists){
    $q2 = "SELECT staff_id FROM staff WHERE staff_id = ? LIMIT 1";
    if($s = $conn->prepare($q2)){
        $s->bind_param('s', $employee_id);
        $s->execute(); $r2 = $s->get_result(); if($r2 && $r2->num_rows>0){ $exists = true; }
        $s->close();
    }
}
if(!$exists){ echo json_encode(['status'=>'error','message'=>'Employee not found']); exit; }

$insert = "INSERT INTO expense_reimbursements (employee_id, expense_type, amount, description, date_requested) VALUES (?, ?, ?, ?, CURDATE())";
if($ins = $conn->prepare($insert)){
    $ins->bind_param('isds', $empIdNum, $expense_type, $amount, $description);
    if($ins->execute()){
        echo json_encode(['status'=>'success','message'=>'Inserted']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Could not insert: '.$ins->error]);
    }
    $ins->close();
} else {
    echo json_encode(['status'=>'error','message'=>'Prepare failed: '.$conn->error]);
}
$conn->close();
?>