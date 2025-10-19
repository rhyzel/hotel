<?php
include '../db.php';
require_once __DIR__ . '/../fpdf186/fpdf.php';

function calculateDeductions($gross) {
    $sss = $gross * 0.045;
    $philhealth = $gross * 0.0275;
    $pagibig = min(100, $gross * 0.02);
    $withholding = ($gross > 20000) ? ($gross - 20000) * 0.20 : 0;
    return [
        'sss' => $sss,
        'philhealth' => $philhealth,
        'pagibig' => $pagibig,
        'withholding' => $withholding,
        'total' => $sss + $philhealth + $pagibig + $withholding
    ];
}

$month = date('F');
$month_num = date('m');
$year = date('Y');
$save_path = __DIR__ . '/../payslips/';
$logo_path = "C:/xampp/htdocs/hotel/hr/payroll/logo.png";
if (!is_dir($save_path)) mkdir($save_path, 0777, true);

if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $del_query = $conn->prepare("SELECT pdf_file FROM payslip WHERE id=?");
    $del_query->bind_param("i",$id);
    $del_query->execute();
    $del_query->bind_result($file);
    $del_query->fetch();
    $del_query->close();
    if($file && file_exists($save_path.$file)) unlink($save_path.$file);
    $del_stmt = $conn->prepare("DELETE FROM payslip WHERE id=?");
    $del_stmt->bind_param("i",$id);
    $del_stmt->execute();
    header("Location: generate_all_payslips.php");
    exit;
}

$staff_result = $conn->query("SELECT * FROM staff");
if($staff_result && $staff_result->num_rows > 0){
    while($staff = $staff_result->fetch_assoc()){
        $staff_id = $staff['staff_id'];
        $base_salary = $staff['base_salary'];
        $hourly_rate = $base_salary / (22*8);

        $stmt_hours = $conn->prepare("SELECT time_in, time_out FROM attendance WHERE staff_id=? AND status='Present' AND MONTH(attendance_date)=? AND YEAR(attendance_date)=?");
        $stmt_hours->bind_param("sii",$staff_id,$month_num,$year);
        $stmt_hours->execute();
        $res_hours = $stmt_hours->get_result();
        $total_hours = 0;
        while($row = $res_hours->fetch_assoc()){
            if($row['time_in'] && $row['time_out']){
                $in = new DateTime($row['time_in']);
                $out = new DateTime($row['time_out']);
                $total_hours += max(0,($out->getTimestamp()-$in->getTimestamp())/3600);
            }
        }
        $total_hours = round($total_hours,2);
        $worked_salary = round($total_hours*$hourly_rate,2);

        $stmt_holidays = $conn->prepare("SELECT time_in,time_out,COALESCE(holiday_percentage,100) as holiday_percentage FROM attendance WHERE staff_id=? AND status='Present' AND MONTH(attendance_date)=? AND YEAR(attendance_date)=?");
        $stmt_holidays->bind_param("sii",$staff_id,$month_num,$year);
        $stmt_holidays->execute();
        $res_holidays = $stmt_holidays->get_result();
        $holiday_hours = 0; $holiday_pay = 0;
        while($row = $res_holidays->fetch_assoc()){
            if($row['time_in'] && $row['time_out']){
                $in = new DateTime($row['time_in']);
                $out = new DateTime($row['time_out']);
                $diff = max(0,($out->getTimestamp()-$in->getTimestamp())/3600);
                $holiday_hours += $diff;
                $holiday_pay += $diff * $hourly_rate * ($row['holiday_percentage']/100);
            }
        }
        $holiday_hours = round($holiday_hours,2);
        $holiday_pay = round($holiday_pay,2);

        $stmt_ot = $conn->prepare("SELECT SUM(hours) as total_ot, SUM(hours * COALESCE(percentage,100)/100) as ot_pay_percentage FROM overtime WHERE staff_id=? AND MONTH(overtime_date)=? AND YEAR(overtime_date)=?");
        $stmt_ot->bind_param("sii",$staff_id,$month_num,$year);
        $stmt_ot->execute();
        $ot_data = $stmt_ot->get_result()->fetch_assoc();
        $total_ot_hours = $ot_data['total_ot'] ?? 0;
        $ot_pay = round(($total_ot_hours*$hourly_rate) + ($ot_data['ot_pay_percentage'] ?? 0),2);

        $stmt_bonus = $conn->prepare("SELECT SUM(amount) as total_bonus FROM bonuses_incentives WHERE staff_id=? AND MONTH(created_at)=? AND YEAR(created_at)=?");
        $stmt_bonus->bind_param("sii",$staff_id,$month_num,$year);
        $stmt_bonus->execute();
        $total_bonus = $stmt_bonus->get_result()->fetch_assoc()['total_bonus'] ?? 0;

        $stmt_reimburse = $conn->prepare("SELECT SUM(amount) as total_reimburse FROM reimbursements WHERE staff_id=? AND status='Approved' AND MONTH(submitted_at)=? AND YEAR(submitted_at)=?");
        $stmt_reimburse->bind_param("sii",$staff_id,$month_num,$year);
        $stmt_reimburse->execute();
        $total_reimburse = $stmt_reimburse->get_result()->fetch_assoc()['total_reimburse'] ?? 0;

        $stmt_other = $conn->prepare("SELECT SUM(amount) as other_deduction FROM deductions WHERE staff_id=? AND month=? AND year=?");
        $stmt_other->bind_param("sii",$staff_id,$month_num,$year);
        $stmt_other->execute();
        $other_deduction = $stmt_other->get_result()->fetch_assoc()['other_deduction'] ?? 0;

        $gross = $worked_salary + $holiday_pay + $ot_pay + $total_bonus + $total_reimburse;
        $deductions = calculateDeductions($gross);
        $total_deductions = $deductions['total']+$other_deduction;
        $net = $gross - $total_deductions;

        $check = $conn->prepare("SELECT id, pdf_file FROM payslip WHERE staff_id=? AND month=? AND year=?");
        $check->bind_param("sii",$staff_id,$month_num,$year);
        $check->execute();
        $check->store_result();
        $check->bind_result($payslip_id,$pdf_file);
        $check->fetch();

<<<<<<< Updated upstream
        $pdf = new FPDF();
        $pdf->AddPage();
        if(file_exists($logo_path)) $pdf->Image($logo_path,10,10,30);
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(0,10,'Hotel La Vista',0,1,'C');
        $pdf->SetFont('Arial','',12);
        $pdf->Cell(0,7,"Payslip for $month $year",0,1,'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(60,7,"Employee ID: ".$staff_id,0,0);
        $pdf->Cell(0,7,"Employee Name: ".$staff['first_name'].' '.$staff['last_name'],0,1);
        $pdf->Cell(60,7,"Position: ".$staff['position_name'],0,0);
        $pdf->Cell(0,7,"Department: ".$staff['department_name'],0,1);
        $pdf->Cell(60,7,"Hire Date: ".$staff['hire_date'],0,1);
        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',11);
        $pdf->SetFillColor(220,220,220);
        $pdf->Cell(120,8,"Description",1,0,'C',true);
        $pdf->Cell(60,8,"Amount (₱)",1,1,'C',true);
        $pdf->SetFont('Arial','',11);
        $pdf->Cell(120,7,"Base Salary",1);
        $pdf->Cell(60,7,number_format($base_salary,2),1,1,'R');
        $pdf->Cell(120,7,"Worked Salary ($total_hours hrs)",1);
        $pdf->Cell(60,7,number_format($worked_salary,2),1,1,'R');
        $pdf->Cell(120,7,"Holiday Pay ($holiday_hours hrs)",1);
        $pdf->Cell(60,7,number_format($holiday_pay,2),1,1,'R');
        $pdf->Cell(120,7,"Overtime Pay ($total_ot_hours hrs)",1);
        $pdf->Cell(60,7,number_format($ot_pay,2),1,1,'R');
        $pdf->Cell(120,7,"Bonuses/Incentives",1);
        $pdf->Cell(60,7,number_format($total_bonus,2),1,1,'R');
        $pdf->Cell(120,7,"Reimbursements",1);
        $pdf->Cell(60,7,number_format($total_reimburse,2),1,1,'R');
        $pdf->Cell(120,7,"Gross Pay",1);
        $pdf->Cell(60,7,number_format($gross,2),1,1,'R');
        $pdf->Cell(120,7,"SSS Deduction",1);
        $pdf->Cell(60,7,number_format($deductions['sss'],2),1,1,'R');
        $pdf->Cell(120,7,"PhilHealth Deduction",1);
        $pdf->Cell(60,7,number_format($deductions['philhealth'],2),1,1,'R');
        $pdf->Cell(120,7,"Pag-IBIG Deduction",1);
        $pdf->Cell(60,7,number_format($deductions['pagibig'],2),1,1,'R');
        $pdf->Cell(120,7,"Withholding Tax",1);
        $pdf->Cell(60,7,number_format($deductions['withholding'],2),1,1,'R');
        $pdf->Cell(120,7,"Other Deductions",1);
        $pdf->Cell(60,7,number_format($other_deduction,2),1,1,'R');
        $pdf->SetFont('Arial','B',11);
        $pdf->Cell(120,7,"Net Pay",1);
        $pdf->Cell(60,7,number_format($net,2),1,1,'R');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','I',10);
        $pdf->Cell(0,7,"This is a system-generated payslip.",0,1,'C');

        $filename = $staff_id."_{$month}.pdf";
        $file_path = $save_path.$filename;
        $pdf->Output('F',$file_path);

        $stmt = $conn->prepare("INSERT INTO payslip (staff_id, month, year, amount, status, sss, philhealth, pagibig, withholding_tax, other_deduction, total_deductions, net_salary, pdf_file) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE pdf_file=VALUES(pdf_file), amount=VALUES(amount), sss=VALUES(sss), philhealth=VALUES(philhealth), pagibig=VALUES(pagibig), withholding_tax=VALUES(withholding_tax), other_deduction=VALUES(other_deduction), total_deductions=VALUES(total_deductions), net_salary=VALUES(net_salary), status='pending'");
        $stmt->bind_param("siidddddddds",$staff_id,$month_num,$year,$gross,$deductions['sss'],$deductions['philhealth'],$deductions['pagibig'],$deductions['withholding'],$other_deduction,$total_deductions,$net,$filename);
        $stmt->execute();

        $check->close();
    }
=======
$pdf = new FPDF('P','mm','A4');
$pdf->AddPage();

$pdf->SetDrawColor(0,0,0);
$pdf->SetLineWidth(0.5);
if(file_exists($logo_path)) $pdf->Image($logo_path, ($pdf->GetPageWidth()-40)/2, 5, 40);

$pdf->Ln(30);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,20,"Payslip for $month $year",0,1,'C'); // added 20px top margin
$pdf->Ln(10);

$pdf->SetFont('Arial','',11);
$pdf->Cell(50,7,"Employee ID:",0,0);
$pdf->Cell(0,7,$staff_id,0,1);
$pdf->Cell(50,7,"Employee Name:",0,0);
$pdf->Cell(0,7,$staff['first_name'].' '.$staff['last_name'],0,1);
$pdf->Cell(50,7,"Position:",0,0);
$pdf->Cell(0,7,$staff['position_name'],0,1);
$pdf->Cell(50,7,"Department:",0,0);
$pdf->Cell(0,7,$staff['department_name'],0,1);
$pdf->Cell(50,7,"Hire Date:",0,0);
$pdf->Cell(0,7,$staff['hire_date'],0,1);
$pdf->Ln(8);

$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(120,8,"Description",1,0,'C',true);
$pdf->Cell(60,8,"Amount (₱)",1,1,'C',true);
$pdf->SetFont('Arial','',11);
$pdf->SetTextColor(0,0,0);

$earnings = [
    ["Base Salary", $base_salary],
    ["Worked Salary ($total_hours hrs)", $worked_salary],
    ["Holiday Pay ($holiday_hours hrs)", $holiday_pay],
    ["Overtime Pay ($total_ot_hours hrs)", $ot_pay],
    ["Bonuses/Incentives", $total_bonus],
    ["Reimbursements", $total_reimburse],
    ["Gross Pay", $gross]
];

foreach($earnings as $e){
    $pdf->Cell(120,7,$e[0],1);
    $pdf->Cell(60,7,number_format($e[1],2),1,1,'R');
}

$pdf->Ln(5);

$pdf->SetFont('Arial','B',11);
$pdf->SetFillColor(0,0,0);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(120,8,"Deductions",1,0,'C',true);
$pdf->Cell(60,8,"Amount (₱)",1,1,'C',true);
$pdf->SetFont('Arial','',11);
$pdf->SetTextColor(0,0,0);

$deductions_arr = [
    ["SSS Deduction", $deductions['sss']],
    ["PhilHealth Deduction", $deductions['philhealth']],
    ["Pag-IBIG Deduction", $deductions['pagibig']],
    ["Withholding Tax", $deductions['withholding']],
    ["Other Deductions", $other_deduction],
    ["Total Deductions", $total_deductions]
];

foreach($deductions_arr as $d){
    $pdf->Cell(120,7,$d[0],1);
    $pdf->Cell(60,7,number_format($d[1],2),1,1,'R');
}

$pdf->SetFont('Arial','B',11);
$pdf->Cell(120,7,"Net Pay",1);
$pdf->Cell(60,7,number_format($net,2),1,1,'R');

$pdf->Ln(10);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,7,"This is a system-generated payslip.",0,1,'C');

$filename = $staff_id."_{$month}.pdf";
$file_path = $save_path.$filename;
$pdf->Output('F',$file_path);

$stmt = $conn->prepare("INSERT INTO payslip (staff_id, month, year, amount, status, sss, philhealth, pagibig, withholding_tax, other_deduction, total_deductions, net_salary, pdf_file) VALUES (?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE pdf_file=VALUES(pdf_file), amount=VALUES(amount), sss=VALUES(sss), philhealth=VALUES(philhealth), pagibig=VALUES(pagibig), withholding_tax=VALUES(withholding_tax), other_deduction=VALUES(other_deduction), total_deductions=VALUES(total_deductions), net_salary=VALUES(net_salary), status='pending'");
$stmt->bind_param("siidddddddds",$staff_id,$month_num,$year,$gross,$deductions['sss'],$deductions['philhealth'],$deductions['pagibig'],$deductions['withholding'],$other_deduction,$total_deductions,$net,$filename);
$stmt->execute();
$check->close();


}
>>>>>>> Stashed changes
}

$sql = "SELECT p.id, p.staff_id, s.first_name, s.last_name, p.pdf_file 
        FROM payslip p 
        JOIN staff s ON p.staff_id = s.staff_id 
        WHERE month=? AND year=?";

$params = [$month_num, $year];
$types = "ii";

if(isset($_GET['staff_id']) && $_GET['staff_id'] != '') {
    $search = "%".$conn->real_escape_string($_GET['staff_id'])."%";
    $sql .= " AND (p.staff_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
    $types .= "sss";
    $params[] = $search;
    $params[] = $search;
    $params[] = $search;
}

$sql .= " ORDER BY s.last_name ASC, s.first_name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$payslips = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Generate All Payslips - Hotel La Vista</title>
<link rel="stylesheet" href="generate_all_payslips.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header class="page-header">
      <h2>Staff Payslips</h2>
      <div class="header-controls">
        <a href="../payroll.php" class="nav-btn">&#8592; Back to Dashboard</a>
        <form method="get" class="filter-form">
            <input type="text" name="staff_id" placeholder="Search Employee ID or Name" value="<?= isset($_GET['staff_id'])?htmlspecialchars($_GET['staff_id']):'' ?>">
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
      </div>
    </header>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Employee Name</th>
            <th>Payslip</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if($payslips && $payslips->num_rows > 0){ ?>
              <?php while($row = $payslips->fetch_assoc()){ ?>
                <tr>
                  <td><?= $row['staff_id'] ?></td>
                  <td><?= htmlspecialchars($row['last_name'].', '.$row['first_name']) ?></td>
                  <td><?= htmlspecialchars($row['pdf_file']) ?></td>
                  <td>
                    <a href="../payslips/<?= urlencode($row['pdf_file']) ?>" target="_blank" class="nav-btn"><i class="fas fa-download"></i> Download</a>
                    <a href="generate_all_payslips.php?delete=<?= $row['id'] ?>" class="nav-btn" onclick="return confirm('Are you sure you want to delete this payslip?')"><i class="fas fa-trash"></i> Delete</a>
                    <a href="generate_all_payslips.php?edit=<?= $row['id'] ?>" class="nav-btn"><i class="fas fa-edit"></i> Regenerate</a>
                  </td>
                </tr>
              <?php } ?>
          <?php } else { ?>
            <tr><td colspan="4">No payslips found.</td></tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
