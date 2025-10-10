<?php
require '../db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=staff_deductions.xls");
header("Pragma: no-cache");
header("Expires: 0");

echo "<table border='1'>";
echo "<tr>
        <th>Employee ID</th>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Gross Salary</th>
        <th>SSS</th>
        <th>PhilHealth</th>
        <th>Pag-IBIG</th>
        <th>Withholding Tax</th>
        <th>Other Deductions</th>
        <th>Total Deductions</th>
        <th>Net Salary</th>
      </tr>";

$month = date('m');
$year = date('Y');

$result = $conn->query("SELECT * FROM staff ORDER BY last_name ASC, first_name ASC");

while ($e = $result->fetch_assoc()) {
    $gross = $e['base_salary'] ?? 0;
    $sss = $gross * 0.045;
    $phil = $gross * 0.0275;
    $pagibig = min(100, $gross * 0.02);
    $tax = ($gross <= 20833) ? 0 : ($gross - 20833) * 0.20;

    $staff_id = $e['staff_id'];
    $deductionQuery = $conn->prepare("SELECT SUM(amount) AS total FROM deductions WHERE staff_id=? AND month=? AND year=?");
    $deductionQuery->bind_param("sii", $staff_id, $month, $year);
    $deductionQuery->execute();
    $other = $deductionQuery->get_result()->fetch_assoc()['total'] ?? 0;

    $totalDeduction = $sss + $phil + $pagibig + $tax + $other;
    $net = $gross - $totalDeduction;

    echo "<tr>
            <td>{$staff_id}</td>
            <td>{$e['last_name']}</td>
            <td>{$e['first_name']}</td>
            <td>".number_format($gross,2)."</td>
            <td>".number_format($sss,2)."</td>
            <td>".number_format($phil,2)."</td>
            <td>".number_format($pagibig,2)."</td>
            <td>".number_format($tax,2)."</td>
            <td>".number_format($other,2)."</td>
            <td>".number_format($totalDeduction,2)."</td>
            <td>".number_format($net,2)."</td>
          </tr>";
}

echo "</table>";
?>
