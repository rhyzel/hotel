<?php
require '../db.php';
require '../../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$staff_id = $_POST['deduction_staff_id'];
$amount = floatval($_POST['other_deduction']);
$reason_type = $_POST['reason_type'];
$reason_desc = $_POST['deduction_reason'];

$proof_path = null;
if (!empty($_FILES['proof_image']['name'])) {
    $uploadDir = 'C:/xampp/htdocs/hotel/hr/agreements/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName = $staff_id.'_'.time().'_'.basename($_FILES['proof_image']['name']);
    $target = $uploadDir.$fileName;
    move_uploaded_file($_FILES['proof_image']['tmp_name'], $target);
    $proof_path = realpath($target);
}

$stmt = $conn->prepare("SELECT first_name, last_name, email, base_salary FROM staff WHERE staff_id=?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$emp = $stmt->get_result()->fetch_assoc();

$fullName = $emp['first_name'].' '.$emp['last_name'];
$email = $emp['email'];
$baseSalary = $emp['base_salary'];

$month = date('m');
$year = date('Y');

$sss = $baseSalary * 0.045;
$philhealth = $baseSalary * 0.0275;
$pagibig = min(100, $baseSalary * 0.02);
$tax = ($baseSalary <= 20833) ? 0 : ($baseSalary - 20833) * 0.20;
$total_deductions = $sss + $philhealth + $pagibig + $tax + $amount;
$net_salary = $baseSalary - $total_deductions;

$check = $conn->prepare("SELECT id FROM payslip WHERE staff_id=? AND month=? AND year=?");
$check->bind_param("sss", $staff_id, $month, $year);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $update = $conn->prepare("UPDATE payslip SET amount=?, sss=?, philhealth=?, pagibig=?, withholding_tax=?, total_deductions=?, net_salary=? WHERE staff_id=? AND month=? AND year=?");
    $update->bind_param("ddddddssss", $baseSalary, $sss, $philhealth, $pagibig, $tax, $total_deductions, $net_salary, $staff_id, $month, $year);
    $update->execute();
} else {
    $insert = $conn->prepare("INSERT INTO payslip (staff_id, month, year, amount, status, sss, philhealth, pagibig, withholding_tax, total_deductions, net_salary) VALUES (?,?,?,'pending',?,?,?,?,?,?,?)");
    $insert->bind_param("sssddddddd", $staff_id, $month, $year, $baseSalary, $sss, $philhealth, $pagibig, $tax, $total_deductions, $net_salary);
    $insert->execute();
}

$deduction = $conn->prepare("INSERT INTO deductions (staff_id, amount, reason_type, description, proof_image, month, year) VALUES (?,?,?,?,?,?,?)");
$deduction->bind_param("sdsssii", $staff_id, $amount, $reason_type, $reason_desc, $proof_path, $month, $year);
$deduction->execute();

$html = "
<h2>Deduction Agreement</h2>
<p><strong>Employee:</strong> {$fullName} ({$staff_id})</p>
<p><strong>Reason Type:</strong> {$reason_type}</p>
<p><strong>Description:</strong> {$reason_desc}</p>
<p><strong>Amount:</strong> PHP ".number_format($amount,2)."</p>
<p>Date Filed: ".date('F d, Y')."</p>";

if ($proof_path) {
    $proof_path = str_replace('\\', '/', $proof_path);
    $html .= "<p><strong>Proof Image:</strong></p><img src='file://{$proof_path}' width='300'>";
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();

$pdfOutput = $dompdf->output();
$pdfFile = 'C:/xampp/htdocs/hotel/hr/agreements/agreement_'.$staff_id.'_'.time().'.pdf';
file_put_contents($pdfFile, $pdfOutput);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="deduction_agreement.pdf"');
echo $pdfOutput;
?>
