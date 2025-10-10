<?php
include '../db.php';

$staff_id = $_GET['staff_id'] ?? '';
$month_filter = $_GET['month_filter'] ?? '';
if (!$staff_id) die("Staff ID required");

$name_stmt = $conn->prepare("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM staff WHERE staff_id = ?");
$name_stmt->bind_param("s", $staff_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();
$staff_name = ($row = $name_result->fetch_assoc()) ? $row['full_name'] : $staff_id;

$sql = "
    SELECT 
        CONCAT(LPAD(month,2,'0'),'/',year) AS payroll_month,
        MAX(amount) AS gross_salary,
        MAX(sss) AS sss,
        MAX(philhealth) AS philhealth,
        MAX(pagibig) AS pagibig,
        MAX(withholding_tax) AS withholding_tax,
        COALESCE((SELECT SUM(amount) FROM deductions d WHERE d.staff_id = p.staff_id AND d.month = p.month AND d.year = p.year), 0) AS other_deductions,
        MAX(total_deductions) AS total_deductions,
        MAX(net_salary) AS net_salary,
        month,
        year
    FROM payslip p
    WHERE staff_id = ?
";
if ($month_filter !== '') $sql .= " AND month = ?";
$sql .= " GROUP BY year, month ORDER BY year DESC, month DESC";

$stmt = $conn->prepare($sql);
if ($month_filter !== '') {
    $stmt->bind_param("ss", $staff_id, $month_filter);
} else {
    $stmt->bind_param("s", $staff_id);
}
$stmt->execute();
$result = $stmt->get_result();
$payrolls = [];
while ($row = $result->fetch_assoc()) $payrolls[] = $row;

$months_result = $conn->query("SELECT DISTINCT LPAD(month,2,'0') AS month FROM payslip WHERE staff_id='$staff_id' ORDER BY month DESC");
$available_months = [];
while ($m = $months_result->fetch_assoc()) $available_months[] = $m['month'];

function monthName($num) {
    return date('F', mktime(0,0,0,(int)$num,1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Payroll Record - <?= htmlspecialchars($staff_name) ?></title>
<link rel="stylesheet" href="view_payroll.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
        <div class="profile-header">
            <div class="header-flex">
                <h1>Payroll Record - <?= htmlspecialchars($staff_name) ?></h1>
            </div>
            <div class="tools-row top-margin">
                <a href="tax_deductions.php" class="nav-btn">&#8592; Back To Deductions</a>
                <form method="get" class="filter-form">
                    <input type="hidden" name="staff_id" value="<?= htmlspecialchars($staff_id) ?>">
                    <select name="month_filter" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        <?php foreach ($available_months as $m): ?>
                        <option value="<?= $m ?>" <?= ($m == $month_filter) ? 'selected' : '' ?>><?= monthName($m) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="export_payroll.php?staff_id=<?= urlencode($staff_id) ?>&month_filter=<?= urlencode($month_filter) ?>" class="export-btn"><i class="fas fa-file-excel"></i> Export</a>
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Payroll Month</th>
                        <th>Gross Salary</th>
                        <th>SSS</th>
                        <th>PhilHealth</th>
                        <th>Pag-IBIG</th>
                        <th>Withholding Tax</th>
                        <th>Other Deductions</th>
                        <th>Total Deductions</th>
                        <th>Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payrolls)): ?>
                        <tr><td colspan="9">No records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($payrolls as $p): ?>
                        <tr>
                            <td><?php list($mon,$yr)=explode('/',$p['payroll_month']); echo monthName($mon).' '.$yr; ?></td>
                            <td><?= number_format($p['gross_salary'],2) ?></td>
                            <td><?= number_format($p['sss'],2) ?></td>
                            <td><?= number_format($p['philhealth'],2) ?></td>
                            <td><?= number_format($p['pagibig'],2) ?></td>
                            <td><?= number_format($p['withholding_tax'],2) ?></td>
                            <td><a href="#" class="openDeduction" data-month="<?= $p['month'] ?>" data-year="<?= $p['year'] ?>">₱<?= number_format($p['other_deductions'],2) ?></a></td>
                            <td><?= number_format($p['total_deductions'],2) ?></td>
                            <td><?= number_format($p['net_salary'],2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="deductionModal" class="modal">
    <div class="modal-content">
        <button class="close-btn">Close</button>
        <h3>Other Deductions</h3>
        <ul id="deductionList"></ul>
    </div>
</div>

<script>
const modal = document.getElementById('deductionModal');
const deductionList = document.getElementById('deductionList');
document.querySelectorAll('.openDeduction').forEach(link=>{
    link.addEventListener('click',e=>{
        e.preventDefault();
        const month = link.dataset.month;
        const year = link.dataset.year;
        deductionList.innerHTML='Loading...';
        modal.style.display='flex';
        fetch(`deduction_details.php?staff_id=<?= urlencode($staff_id) ?>&month=${month}&year=${year}`)
        .then(res=>res.json())
        .then(data=>{
            if(data.length===0) deductionList.innerHTML='<li>No deductions.</li>';
            else deductionList.innerHTML=data.map(d=>`<li>${d.reason_type} - ${d.description}: ₱${parseFloat(d.amount).toFixed(2)} ${d.proof_image? `<a href='${d.proof_image}' target='_blank' class='proof-link'>[View Proof]</a>`: ''}</li>`).join('');
        });
    });
});
document.querySelector('.close-btn').addEventListener('click',()=>{modal.style.display='none';});
window.addEventListener('click',e=>{if(e.target===modal) modal.style.display='none';});
</script>
</body>
</html>
