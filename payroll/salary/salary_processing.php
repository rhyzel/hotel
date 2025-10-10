<?php
include '../db.php';

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

$month = date('m');
$year = date('Y');

$filter = $_GET['filter'] ?? 'Active';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM staff WHERE 1=1";
if ($filter !== 'All') $query .= " AND employment_status='" . mysqli_real_escape_string($conn, $filter) . "'";
if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND (staff_id LIKE '%$search%' OR first_name LIKE '%$search%' OR last_name LIKE '%$search%')";
}
$query .= " ORDER BY last_name ASC, first_name ASC";
$staffResult = mysqli_query($conn, $query);

$holidaysResult = $conn->query("SELECT id, name, date, percentage FROM holidays");
$holidays = [];
if ($holidaysResult && $holidaysResult->num_rows > 0) {
    while ($row = $holidaysResult->fetch_assoc()) {
        $holidays[date('Y-m-d', strtotime($row['date']))] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Salary Processing - Hotel La Vista</title>
<link rel="stylesheet" href="salary_processing.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header class="page-header">
      <h2>HOTEL LA VISTA</h2>
      <p>Salary For - <?= date('F Y'); ?></p>
      <div class="header-controls">
        <a href="http://localhost/hotel/hr/payroll/payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>
        <form method="get" class="filter-form">
          <select name="filter" onchange="this.form.submit()">
            <option value="All" <?= $filter=='All' ? 'selected' : '' ?>>All</option>
            <option value="Active" <?= $filter=='Active' ? 'selected' : '' ?>>Active</option>
            <option value="Inactive" <?= $filter=='Inactive' ? 'selected' : '' ?>>Inactive</option>
            <option value="Probation" <?= $filter=='Probation' ? 'selected' : '' ?>>Probation</option>
            <option value="Resigned" <?= $filter=='Resigned' ? 'selected' : '' ?>>Resigned</option>
            <option value="Terminated" <?= $filter=='Terminated' ? 'selected' : '' ?>>Terminated</option>
            <option value="Floating" <?= $filter=='Floating' ? 'selected' : '' ?>>Floating</option>
            <option value="Lay Off" <?= $filter=='Lay Off' ? 'selected' : '' ?>>Lay Off</option>
          </select>
          <input type="text" name="search" placeholder="Search employee..." value="<?= htmlspecialchars($search); ?>">
          <button type="submit">Search</button>
        </form>
        <form method="post" action="export_salary.php" class="export-form">
          <button type="submit" class="nav-btn"><i class="fas fa-file-csv"></i> Export Salary</button>
        </form>
        <form method="post" action="generate_all_payslips.php" class="export-form">
          <button type="submit" class="nav-btn"><i class="fas fa-file-invoice"></i> Generate Payslips for All</button>
        </form>
      </div>
    </header>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Base Salary</th>
            <th>Hourly Rate</th>
            <th>Total Hours Worked</th>
            <th>Worked Salary</th>
            <th>Holidays Worked</th>
            <th>Holiday Pay</th>
            <th>Overtime Hours</th>
            <th>Overtime Pay</th>
            <th>Bonuses/Incentives</th>
            <th>Reimbursements</th>
            <th>Gross Pay</th>
            <th>Deductions</th>
            <th>Salary Dispute</th>
            <th>Net Pay</th>
          </tr>
        </thead>
        <tbody>
<?php
if ($staffResult && mysqli_num_rows($staffResult) > 0) {
    while ($staff = mysqli_fetch_assoc($staffResult)) {
        $staff_id = $staff['staff_id'];
        $name = $staff['last_name'] . ', ' . $staff['first_name'];
        $base_salary = $staff['base_salary'];
        $hourly_rate = $staff['hourly_rate'] > 0 ? $staff['hourly_rate'] : ($base_salary / (22 * 8));

        $stmt_hours = $conn->prepare("SELECT time_in, time_out, attendance_date FROM attendance WHERE staff_id=? AND status='Present'");
        $stmt_hours->bind_param("s", $staff_id);
        $stmt_hours->execute();
        $result_hours = $stmt_hours->get_result();

        $total_hours = 0;
        $holiday_hours = 0;
        $holiday_pay = 0;

        while ($row = $result_hours->fetch_assoc()) {
            if ($row['time_in'] && $row['time_out']) {
                $in = new DateTime($row['time_in']);
                $out = new DateTime($row['time_out']);
                $diff = max(0, ($out->getTimestamp() - $in->getTimestamp()) / 3600);
                $date = date('Y-m-d', strtotime($row['attendance_date']));
                if (isset($holidays[$date])) {
                    $holiday_hours += $diff;
                    $holiday_pay += $diff * $hourly_rate * ($holidays[$date]['percentage'] / 100);
                } else {
                    $total_hours += $diff;
                }
            }
        }

        $total_hours = round($total_hours,2);
        $worked_salary = round($total_hours * $hourly_rate,2);
        $holiday_hours = round($holiday_hours,2);
        $holiday_pay = round($holiday_pay,2);

        $stmt_ot = $conn->prepare("SELECT SUM(hours) as total_ot, SUM(hours * percentage / 100) as ot_pay_percentage FROM overtime WHERE staff_id=? AND MONTH(overtime_date)=? AND YEAR(overtime_date)=?");
        $stmt_ot->bind_param("sii", $staff_id, $month, $year);
        $stmt_ot->execute();
        $ot_data = $stmt_ot->get_result()->fetch_assoc();
        $total_ot_hours = $ot_data['total_ot'] ?? 0;
        $ot_pay = round(($total_ot_hours * $hourly_rate) + ($ot_data['ot_pay_percentage'] ?? 0),2);

        $stmt_bonus = $conn->prepare("SELECT SUM(amount) as total_bonus FROM bonuses_incentives WHERE staff_id=? AND MONTH(created_at)=? AND YEAR(created_at)=?");
        $stmt_bonus->bind_param("sii", $staff_id, $month, $year);
        $stmt_bonus->execute();
        $total_bonus = $stmt_bonus->get_result()->fetch_assoc()['total_bonus'] ?? 0;

        $stmt_reimburse = $conn->prepare("SELECT SUM(amount) as total_reimburse FROM reimbursements WHERE staff_id=? AND status='Approved' AND MONTH(submitted_at)=? AND YEAR(submitted_at)=?");
        $stmt_reimburse->bind_param("sii", $staff_id, $month, $year);
        $stmt_reimburse->execute();
        $total_reimburse = $stmt_reimburse->get_result()->fetch_assoc()['total_reimburse'] ?? 0;

        $stmt_dispute = $conn->prepare("SELECT amount FROM salary_dispute WHERE staff_id=?");
        $stmt_dispute->bind_param("s", $staff_id);
        $stmt_dispute->execute();
        $dispute_amount = $stmt_dispute->get_result()->fetch_assoc()['amount'] ?? 0;

        $gross = $worked_salary + $holiday_pay + $ot_pay + $total_bonus + $total_reimburse;
        $deductions = calculateDeductions($gross);
        $net = $gross - $deductions['total'] + $dispute_amount;

        echo "<tr>
            <td>{$staff_id}</td>
            <td><a href='../staff/view_employee.php?staff_id={$staff_id}' class='staff-link'>{$name}</a></td>
            <td>".number_format($base_salary,2)."</td>
            <td>".number_format($hourly_rate,2)."</td>
            <td>{$total_hours}</td>
            <td>".number_format($worked_salary,2)."</td>
            <td>{$holiday_hours}</td>
            <td>".number_format($holiday_pay,2)."</td>
            <td>{$total_ot_hours}</td>
            <td>".number_format($ot_pay,2)."</td>
            <td>".number_format($total_bonus,2)."</td>
            <td>".number_format($total_reimburse,2)."</td>
            <td>".number_format($gross,2)."</td>
            <td>".number_format($deductions['total'],2)."</td>
            <td>".number_format($dispute_amount,2)."</td>
            <td>".number_format($net,2)."</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='16'>No employees found.</td></tr>";
}
?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
