<?php
include_once(__DIR__ . '/../../db_connector.php');
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status'=>'error','message'=>'POST only']); exit; }
$employee_id = $_POST['employee_id'] ?? '';
if(empty($employee_id)) { echo json_encode(['status'=>'error','message'=>'employee_id required']); exit; }
$sss = isset($_POST['sss']) ? floatval($_POST['sss']) : 0.0;
$phil = isset($_POST['philhealth']) ? floatval($_POST['philhealth']) : 0.0;
$pagibig = isset($_POST['pagibig']) ? floatval($_POST['pagibig']) : 0.0;
$tax = isset($_POST['tax']) ? floatval($_POST['tax']) : 0.0;

// ensure employee exists (ceo table) - accept numeric id or staff_id fallback
$empIdNum = intval($employee_id);
$exists = false;
$q = "SELECT id FROM ceo WHERE id = ? LIMIT 1";
if($stmt = $conn->prepare($q)){
    $stmt->bind_param('i', $empIdNum);
    $stmt->execute(); $res = $stmt->get_result(); if($res && $res->num_rows>0){ $exists = true; }
    $stmt->close();
}
if(!$exists){
    // try staff table
    $q2 = "SELECT staff_id FROM staff WHERE staff_id = ? LIMIT 1";
    if($s = $conn->prepare($q2)){
        $s->bind_param('s', $employee_id);
        $s->execute(); $r2 = $s->get_result(); if($r2 && $r2->num_rows>0) { $exists = true; }
        $s->close();
    }
}
if(!$exists){ echo json_encode(['status'=>'error','message'=>'Employee not found']); exit; }

$insert = "INSERT INTO payroll_deductions (employee_id, sss, philhealth, pagibig, tax, other_deductions) VALUES (?, ?, ?, ?, ?, 0.00)";
if($ins = $conn->prepare($insert)){
    $ins->bind_param('idddd', $empIdNum, $sss, $phil, $pagibig, $tax);
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