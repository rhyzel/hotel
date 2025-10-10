<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

include '../db.php';
$staff_id = $_SESSION['staff_id'];

$staffQuery = "SELECT * FROM staff WHERE staff_id=?";
$stmt = $conn->prepare($staffQuery);
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

$filterMonth = $_GET['filter_month'] ?? '';
$searchTerm = trim($_GET['search'] ?? '');

$payslipQuery = "
    SELECT p.*, s.first_name, s.last_name
    FROM payslip p
    JOIN staff s ON p.staff_id = s.staff_id
    INNER JOIN (
        SELECT staff_id, month, year, MAX(id) AS latest_id
        FROM payslip
        WHERE staff_id=?
        GROUP BY staff_id, year, month
    ) t ON p.id = t.latest_id
    WHERE p.staff_id=?
";

$params = [$staff_id, $staff_id];
$types = "ss";

if ($filterMonth) {
    $monthNum = date('m', strtotime($filterMonth));
    $payslipQuery .= " AND p.month=?";
    $params[] = $monthNum;
    $types .= "i";
}

if ($searchTerm) {
    $payslipQuery .= " AND (p.staff_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
    $searchWildcard = "%$searchTerm%";
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $params[] = $searchWildcard;
    $types .= "sss";
}

$payslipQuery .= " ORDER BY p.year DESC, p.month DESC";

$stmt = $conn->prepare($payslipQuery);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$payslips = $stmt->get_result();

$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$payslipPath = '/hotel/hr/payroll/payslips/';

$reimbursementReasons = ['Travel','Meals','Supplies','Other'];
$disputeReasons = ['Missing Payment','Incorrect Amount','Other'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Portal - Hotel La Vista</title>
<link rel="stylesheet" href="portal.css">
<link rel="stylesheet" href="/fonts/fontawesome/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
       <div class="profile-header">
    <h1>Welcome, <?= htmlspecialchars($staff['first_name'].' '.$staff['last_name']) ?>!</h1>
    <div class="tools-row">
        <div class="tools-left">
            <a href="http://localhost/hotel/hr/payroll/payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>
            <a href="logout.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Logout</a>
        </div>
        <form method="get" class="filter-form">
            <input type="text" name="search" placeholder="Search by ID or Name" value="<?= htmlspecialchars($searchTerm) ?>">
            <select name="filter_month" onchange="this.form.submit()">
                <option value="">Filter by Month</option>
                <?php foreach ($months as $m): ?>
                    <option value="<?= $m ?>" <?= ($filterMonth == $m ? 'selected' : '') ?>><?= $m ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><i class="fas fa-search"></i> Search</button>
        </form>
        <div class="tools-right">
            <button class="nav-btn" onclick="document.getElementById('reimbursementModal').style.display='flex'">
                <i class="fas fa-money-bill"></i> Request Reimbursement
            </button>
            <button class="nav-btn" onclick="document.getElementById('disputeModal').style.display='flex'">
                <i class="fas fa-exclamation-triangle"></i> File Dispute
            </button>
            <a href="all_requests.php" class="nav-btn">
                <i class="fas fa-list"></i> View All Requests
            </a>
        </div>
    </div>
</div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Net Salary (PHP)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($payslips && $payslips->num_rows > 0): ?>
                        <?php while ($p = $payslips->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['staff_id'].' - '.$p['first_name'].' '.$p['last_name']) ?></td>
                            <td><?= date('F Y', strtotime($p['year'].'-'.$p['month'].'-01')) ?></td>
                            <td><?= number_format($p['net_salary'], 2) ?></td>
                            <td>
                                <?php if(!empty($p['pdf_file']) && file_exists($_SERVER['DOCUMENT_ROOT'].$payslipPath.$p['pdf_file'])): ?>
                                    <a href="<?= $payslipPath.urlencode($p['pdf_file']) ?>" target="_blank" class="export-btn"><i class="fas fa-eye"></i> View</a>
                                    <a href="<?= $payslipPath.urlencode($p['pdf_file']) ?>" download class="export-btn"><i class="fas fa-download"></i> Download</a>
                                <?php else: ?>
                                    File not found
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No payslips found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="modal" id="reimbursementModal">
            <div class="modal-content">
                <h2>Request Reimbursement</h2>
                <form method="post" action="submit_reimbursement.php" enctype="multipart/form-data">
                    <label>Amount</label>
                    <input type="number" step="0.01" name="amount" required>
                    <label>Reason</label>
                    <select name="reason" required>
                        <?php foreach($reimbursementReasons as $r): ?>
                            <option value="<?= $r ?>"><?= $r ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Notes</label>
                    <textarea name="notes"></textarea>
                    <label>Proof (JPG only)</label>
                    <input type="file" name="proof_file" accept=".jpg,.jpeg">
                    <div class="modal-actions">
                        <button type="submit" class="submit-btn">Submit</button>
                        <button type="button" class="close-btn" onclick="document.getElementById('reimbursementModal').style.display='none'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

       <div class="modal" id="disputeModal">
    <div class="modal-content">
        <h2>File Salary Dispute</h2>
        <form method="post" action="submit_dispute.php" enctype="multipart/form-data">
            <label>Payout Date</label>
            <input type="date" name="payout_date" required>
            <label>Amount</label>
            <input type="number" step="0.01" name="dispute_amount" required>
            <label>Reason</label>
            <select name="discrepancy_reason" required>
                <?php foreach($disputeReasons as $r): ?>
                    <option value="<?= $r ?>"><?= $r ?></option>
                <?php endforeach; ?>
            </select>
            <label>Notes</label>
            <textarea name="notes"></textarea>
            <label>Proof (JPG only)</label>
            <input type="file" name="proof_file" accept=".jpg,.jpeg">
            <div class="modal-actions">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="button" class="close-btn" onclick="document.getElementById('disputeModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
