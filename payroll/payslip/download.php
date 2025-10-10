<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

include '../db.php';
if (!isset($_GET['payslip_id'])) {
    die('Invalid request');
}

$payslip_id = intval($_GET['payslip_id']);
$stmt = $conn->prepare("SELECT * FROM payslip WHERE id=? AND staff_id=?");
$stmt->bind_param("is", $payslip_id, $_SESSION['staff_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die('Payslip not found');
}

$p = $result->fetch_assoc();

require(__DIR__ . '/../fpdf186/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Hotel La Vista - Payslip',0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Employee: ' . $_SESSION['staff_id'],0,1);
$pdf->Cell(0,8,'Month: ' . $p['month'] . ' ' . $p['year'],0,1);
$pdf->Ln(5);

$pdf->SetFillColor(230,230,230);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,8,'Earnings / Deductions',1,0,'C',true);
$pdf->Cell(40,8,'Amount',1,1,'C',true);

$pdf->SetFont('Arial','',12);
$pdf->Cell(80,8,'Net Salary',1,0);
$pdf->Cell(40,8,number_format($p['net_salary'],2),1,1);

$pdf->Cell(80,8,'SSS',1,0);
$pdf->Cell(40,8,number_format($p['sss'],2),1,1);

$pdf->Cell(80,8,'PhilHealth',1,0);
$pdf->Cell(40,8,number_format($p['philhealth'],2),1,1);

$pdf->Cell(80,8,'Pag-IBIG',1,0);
$pdf->Cell(40,8,number_format($p['pagibig'],2),1,1);

$pdf->Cell(80,8,'Withholding Tax',1,0);
$pdf->Cell(40,8,number_format($p['withholding_tax'],2),1,1);

$pdf->Cell(80,8,'Other Deduction',1,0);
$pdf->Cell(40,8,number_format($p['other_deduction'],2),1,1);

$pdf->Cell(80,8,'Total Deductions',1,0);
$pdf->Cell(40,8,number_format($p['total_deductions'],2),1,1);

$pdf->Ln(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,'Net Pay: ' . number_format($p['net_salary'],2),0,1,'C');

$pdf->Output('I','Payslip_'.$p['month'].'_'.$p['year'].'.pdf');
exit;
