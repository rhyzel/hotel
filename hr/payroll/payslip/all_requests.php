<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header('Location: login.php');
    exit;
}

include '../db.php';
$staff_id = $_SESSION['staff_id'];

$stmt = $conn->prepare("SELECT * FROM staff WHERE staff_id=?");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("SELECT * FROM reimbursements WHERE staff_id=? ORDER BY submitted_at DESC");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$reimbursements = $stmt->get_result();

$stmt = $conn->prepare("SELECT * FROM salary_dispute WHERE staff_id=? ORDER BY date_filed DESC");
$stmt->bind_param("s", $staff_id);
$stmt->execute();
$disputes = $stmt->get_result();

$reimbursementPath = '/hotel/hr/payroll/uploads/reimbursements/';
$disputePath = '/hotel/hr/payroll/uploads/disputes/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>All Requests - Hotel La Vista</title>
<link rel="stylesheet" href="all_requests.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
        <div class="profile-header">
            <h1>All Requests</h1>
            <div class="tools-row">
                <a href="portal.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Back</a>
                <form method="get" class="search-form">
                    <input type="text" name="search" placeholder="Search requests...">
                    <button type="submit"><i class="fas fa-search"></i> Filter</button>
                </form>
                <a href="export_requests.php" class="export-btn"><i class="fas fa-file-export"></i> Export</a>
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">Reimbursement Requests</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Date Submitted</th>
                            <th>Amount (PHP)</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($r = $reimbursements->fetch_assoc()): ?>
                        <tr>
                            <td><?= date('F d, Y', strtotime($r['submitted_at'])) ?></td>
                            <td><?= number_format($r['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($r['reason']) ?></td>
                            <td><?= htmlspecialchars($r['status']) ?></td>
                            <td>
                                <?php 
                                $proofFile = basename($r['proof_file']);
                                if (!empty($proofFile) && file_exists($_SERVER['DOCUMENT_ROOT'].$reimbursementPath.$proofFile)): ?>
                                    <a href="<?= $reimbursementPath.urlencode($proofFile) ?>" target="_blank" class="export-btn"><i class="fas fa-eye"></i> View</a>
                                    <a href="<?= $reimbursementPath.urlencode($proofFile) ?>" download class="export-btn"><i class="fas fa-download"></i> Download</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section-card">
            <h2 class="section-title">Salary Disputes</h2>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Date Filed</th>
                            <th>Payout Date</th>
                            <th>Amount (PHP)</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Proof</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($d = $disputes->fetch_assoc()):
                        $payout_date = $amount = $reason = '';
                        if (!empty($d['dispute_details'])) {
                            preg_match('/Payout Date:\s*(.+)/i', $d['dispute_details'], $matches);
                            $payout_date = $matches[1] ?? '';
                            preg_match('/Amount:\s*(.+)/i', $d['dispute_details'], $matches);
                            $amount = $matches[1] ?? 0;
                            preg_match('/Reason:\s*(.+)/i', $d['dispute_details'], $matches);
                            $reason = $matches[1] ?? '';
                        }
                        $proofFile = basename($d['proof_file'] ?? '');
                    ?>
                        <tr>
                            <td><?= date('F d, Y', strtotime($d['date_filed'])) ?></td>
                            <td><?= htmlspecialchars($payout_date) ?></td>
                            <td><?= number_format((float)$amount, 2) ?></td>
                            <td><?= htmlspecialchars($reason) ?></td>
                            <td><?= htmlspecialchars($d['status']) ?></td>
                            <td>
                                <?php if ($proofFile && file_exists($_SERVER['DOCUMENT_ROOT'].$disputePath.$proofFile)): ?>
                                    <a href="<?= $disputePath.urlencode($proofFile) ?>" target="_blank" class="export-btn"><i class="fas fa-eye"></i> View</a>
                                    <a href="<?= $disputePath.urlencode($proofFile) ?>" download class="export-btn"><i class="fas fa-download"></i> Download</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
