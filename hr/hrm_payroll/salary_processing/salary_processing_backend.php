<?php
include_once(__DIR__ . '/../db_connector.php');
header('Content-Type: application/json');

if (!isset($conn) || $conn === null) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$employee_id = $_POST['employee_id'] ?? '';
$basic = floatval($_POST['basic_salary'] ?? 0);
$allow = floatval($_POST['allowances'] ?? 0);
$overtime = floatval($_POST['overtime'] ?? 0);
$gross = $basic + $allow + $overtime;
$sss = $gross * 0.045;
$philhealth = $gross * 0.0275;
$pagibig = min(100, $gross * 0.02);
$withholding = $gross > 20000 ? ($gross - 20000) * 0.2 : 0;
$totalDeductions = $sss + $philhealth + $pagibig + $withholding;
$net = $gross - $totalDeductions;
$payroll_month = $_POST['payroll_month'] ?? '';
$remarks = $_POST['remarks'] ?? '';

$full_name = '';
$stmt = $conn->prepare("SELECT first_name,last_name FROM staff WHERE staff_id=?");
$stmt->bind_param("s",$employee_id);
$stmt->execute();
$res = $stmt->get_result();
if($row=$res->fetch_assoc()){
    $full_name = trim($row['first_name'].' '.$row['last_name']);
}
$stmt->close();

$insert = $conn->prepare("INSERT INTO payroll_records (employee_id, full_name, base_salary, allowance, overtime_pay, total_deductions, gross_salary, net_salary, payroll_month, remarks) VALUES (?,?,?,?,?,?,?,?,?,?)");
$insert->bind_param("isddddddss",$employee_id,$full_name,$basic,$allow,$overtime,$totalDeductions,$gross,$net,$payroll_month,$remarks);

if($insert->execute()){
    echo json_encode(["status"=>"success","message"=>"Salary record saved successfully"]);
}else{
    echo json_encode(["status"=>"error","message"=>"Failed to save salary record: ".$insert->error]);
}
$insert->close();
$conn->close();
?>
