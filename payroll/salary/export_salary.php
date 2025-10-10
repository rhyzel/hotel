<?php
include '../db.php';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=salary_export_'.date('Y-m-d').'.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Employee ID','Name','Base Salary','Days Present','Overtime Hours','Bonuses/Incentives','Gross Pay','Deductions','Net Pay']);

$month = date('m');
$year = date('Y');

$filter = $_POST['filter'] ?? 'Active';
$search = $_POST['search'] ?? '';

$query = "SELECT * FROM staff WHERE 1=1";
if ($filter !== 'All') {
    $query .= " AND employment_status='" . mysqli_real_escape_string($conn, $filter) . "'";
}
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (staff_id LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
}
$staffResult = mysqli_query($conn, $query);

function calculateDeductions($gross) {
    $sss = $gross * 0.045;
    $philhealth = $gross * 0.0275;
    $pagibig = min(100, $gross * 0.02);
    $withholding = ($gross > 20000) ? ($gross - 20000) * 0.20 : 0;
    return $sss + $philhealth + $pagibig + $withholding;
}

if ($staffResult && mysqli_num_rows($staffResult) > 0) {
    while ($staff = mysqli_fetch_assoc($staffResult)) {
        $staff_id = $staff['staff_id'];
        $name = $staff['first_name'] . ' ' . $staff['last_name'];
        $base_salary = $staff['base_salary'];
        $daily_rate = $base_salary / 22;
        $hourly_rate = $daily_rate / 8;

        $stmt = $conn->prepare("SELECT COUNT(*) as days_present FROM attendance WHERE staff_id=? AND status='Present' AND MONTH(attendance_date)=? AND YEAR(attendance_date)=?");
        $stmt->bind_param("sii", $staff_id, $month, $year);
        $stmt->execute();
        $days_present = $stmt->get_result()->fetch_assoc()['days_present'];

        $stmt_ot = $conn->prepare("SELECT SUM(hours) as total_ot FROM overtime WHERE staff_id=? AND MONTH(overtime_date)=? AND YEAR(overtime_date)=?");
        $stmt_ot->bind_param("sii", $staff_id, $month, $year);
        $stmt_ot->execute();
        $total_ot = $stmt_ot->get_result()->fetch_assoc()['total_ot'] ?? 0;

        $stmt_bonus = $conn->prepare("SELECT SUM(amount) as total_bonus FROM bonuses_incentives WHERE staff_id=? AND MONTH(created_at)=? AND YEAR(created_at)=?");
        $stmt_bonus->bind_param("sii", $staff_id, $month, $year);
        $stmt_bonus->execute();
        $total_bonus = $stmt_bonus->get_result()->fetch_assoc()['total_bonus'] ?? 0;

        $attendance_pay = $days_present * $daily_rate;
        $ot_pay = $total_ot * $hourly_rate * 1.25;
        $gross = $attendance_pay + $ot_pay + $total_bonus;
        $deductions = calculateDeductions($gross);
        $net = $gross - $deductions;

        fputcsv($output, [$staff_id, $name, number_format($base_salary,2), $days_present, $total_ot, number_format($total_bonus,2), number_format($gross,2), number_format($deductions,2), number_format($net,2)]);
    }
}
fclose($output);
exit;
