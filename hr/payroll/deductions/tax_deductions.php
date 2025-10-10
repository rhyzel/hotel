<?php
require '../db.php';
require '../../dompdf/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

$search = $_GET['search'] ?? '';
$employees = [];

if ($search) {
    $searchTerm = "%{$search}%";
    $stmt = $conn->prepare("
        SELECT * FROM staff 
        WHERE staff_id LIKE ? OR first_name LIKE ? OR last_name LIKE ? 
        ORDER BY last_name ASC, first_name ASC
    ");
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) $employees[] = $row;
} else {
    $result = $conn->query("SELECT * FROM staff ORDER BY last_name ASC, first_name ASC");
    while ($row = $result->fetch_assoc()) $employees[] = $row;
}


function calcSSS($gross) { return $gross * 0.045; }
function calcPhilHealth($gross) { return $gross * 0.0275; }
function calcPagIBIG($gross) { return min(100, $gross * 0.02); }
function calcTax($gross) { return ($gross <= 20833) ? 0 : ($gross - 20833) * 0.20; }

$month = date('m');
$year = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>HR Payroll - Taxes & Deductions</title>
<link rel="stylesheet" href="tax_deductions.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
<div class="overlay">
    <div class="container">
        <div class="profile-header">
            <div class="header-flex">
                <h1>Taxes & Government Deductions</h1>
            </div>
            <div class="tools-row">
                <a href="http://localhost/hotel/hr/payroll/payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>
                <form method="get" class="search-form">
                    <input type="text" name="search" placeholder="Search by ID or Name" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
                <a href="export_all.php" class="export-btn"><i class="fas fa-file-excel"></i> Export All to Excel</a>
                <button type="button" class="file-deduction-btn" id="openDeductionModal"><i class="fas fa-pen"></i> File Other Deduction</button>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Gross Salary (PHP)</th>
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
                    <?php foreach ($employees as $e):
                        $gross = $e['base_salary'] ?? 0;
                        $sss = calcSSS($gross);
                        $phil = calcPhilHealth($gross);
                        $pagibig = calcPagIBIG($gross);
                        $tax = calcTax($gross);

                        $staff_id = $e['staff_id'];
                        $deductionQuery = $conn->prepare("SELECT SUM(amount) AS total FROM deductions WHERE staff_id=? AND month=? AND year=?");
                        $deductionQuery->bind_param("sii", $staff_id, $month, $year);
                        $deductionQuery->execute();
                        $deductionResult = $deductionQuery->get_result()->fetch_assoc();
                        $other = $deductionResult['total'] ?? 0;

                        $total = $sss + $phil + $pagibig + $tax + $other;
                        $net = $gross - $total;
                    ?>
                   <td>
    <a href="view_payroll.php?staff_id=<?= urlencode($e['staff_id']) ?>">
        <?= htmlspecialchars($e['last_name'].', '.$e['first_name']) ?>
    </a>
</td>

                        <td><a href="view_payroll.php?staff_id=<?= urlencode($e['staff_id']) ?>"><?= htmlspecialchars($e['first_name'].' '.$e['last_name']) ?></a></td>
                        <td><?= number_format($gross,2) ?></td>
                        <td><?= number_format($sss,2) ?></td>
                        <td><?= number_format($phil,2) ?></td>
                        <td><?= number_format($pagibig,2) ?></td>
                        <td><?= number_format($tax,2) ?></td>
                        <td><?= number_format($other,2) ?></td>
                        <td><?= number_format($total,2) ?></td>
                        <td><?= number_format($net,2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="deductionModal" class="modal">
    <div class="modal-content">
        <h2>File Other Deduction</h2>
        <form method="post" action="generate_agreement.php" target="_blank" enctype="multipart/form-data">
            <label for="deduction_staff_id">Select Employee</label>
            <select name="deduction_staff_id" id="deduction_staff_id" required>
                <option value="">-- Select --</option>
                <?php
                $staffRes = $conn->query("SELECT staff_id, first_name, last_name FROM staff ORDER BY first_name ASC");
                while ($s = $staffRes->fetch_assoc()):
                ?>
                <option value="<?= htmlspecialchars($s['staff_id']) ?>">
                    <?= htmlspecialchars($s['first_name'].' '.$s['last_name']) ?> (<?= htmlspecialchars($s['staff_id']) ?>)
                </option>
                <?php endwhile; ?>
            </select>

            <label for="deduction_month">Month</label>
            <select name="deduction_month" id="deduction_month" required>
                <?php for($m=1;$m<=12;$m++): ?>
                    <option value="<?= $m ?>" <?= ($m == date('m'))?'selected':'' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                <?php endfor; ?>
            </select>

            <label for="deduction_year">Year</label>
            <select name="deduction_year" id="deduction_year" required>
                <?php for($y=date('Y')-5;$y<=date('Y');$y++): ?>
                    <option value="<?= $y ?>" <?= ($y == date('Y'))?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>

            <label for="other_deduction">Deduction Amount (PHP)</label>
            <input type="number" step="0.01" min="0" name="other_deduction" id="other_deduction" required>

            <label for="reason_type">Reason Type</label>
            <select name="reason_type" id="reason_type" required>
                <option value="">-- Select --</option>
                <option value="Loan">Loan</option>
                <option value="Penalty">Penalty</option>
                <option value="Property Damage">Property Damage</option>
                <option value="Cash Advance">Cash Advance</option>
                <option value="Others">Others</option>
            </select>

            <label for="deduction_reason">Description</label>
            <textarea name="deduction_reason" id="deduction_reason" rows="3" required placeholder="Additional details if needed"></textarea>

            <label for="proof_image">Upload Proof (Image)</label>
            <input type="file" name="proof_image" id="proof_image" accept="image/*" required>

            <div class="modal-actions">
                <button type="submit" class="submit-btn"><i class="fas fa-file-contract"></i> Generate Agreement</button>
                <button type="button" class="close-btn">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('deductionModal');
const openBtn = document.getElementById('openDeductionModal');
const closeBtn = document.querySelector('.close-btn');
openBtn.addEventListener('click', () => { modal.style.display = 'flex'; });
closeBtn.addEventListener('click', () => { modal.style.display = 'none'; });
window.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
</script>
</body>
</html>
