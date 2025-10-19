<?php
include_once(__DIR__ . '/../../db_connector.php');
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['status'=>'error','message'=>'POST only']); exit; }
$employee_id = $_POST['employee_id'] ?? '';
$period = $_POST['period'] ?? ''; // expected YYYY-MM
if(empty($employee_id) || empty($period)) { echo json_encode(['status'=>'error','message'=>'employee_id and period required']); exit; }
$parts = explode('-', $period);
if(count($parts) !== 2){ echo json_encode(['status'=>'error','message'=>'invalid period']); exit; }
$year = intval($parts[0]); $month = intval($parts[1]);

// Resolve employee existence
$empIdNum = intval($employee_id);
$exists = false;
$q = "SELECT id FROM ceo WHERE id = ? LIMIT 1";
if($stmt = $conn->prepare($q)){
    $stmt->bind_param('i', $empIdNum);
    $stmt->execute(); $res = $stmt->get_result(); if($res && $res->num_rows>0) $exists=true;
    $stmt->close();
}
if(!$exists){ echo json_encode(['status'=>'error','message'=>'employee not found']); exit; }

// Insert payslip record (minimal fields). The payroll generation flow should compute amounts separately; here we record availability.
$insert = "INSERT INTO payslips (payroll_id, employee_id, issue_date, pdf_path) VALUES (NULL, ?, NOW(), NULL)";
if($stmt = $conn->prepare($insert)){
    $stmt->bind_param('i', $empIdNum);
    if($stmt->execute()){
        echo json_encode(['status'=>'success','message'=>'Payslip record created']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Could not insert: '.$stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status'=>'error','message'=>'Prepare failed: '.$conn->error]);
}
$conn->close();
?>