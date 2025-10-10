<?php
include '../db.php';

$filterMonth = $_GET['filter_month'] ?? '';
$searchTerm = trim($_GET['search'] ?? '');
$months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
$disputePath = '/hotel/hr/payroll/uploads/disputes/';

$query = "SELECT sd.*, s.first_name, s.last_name 
          FROM salary_dispute sd
          JOIN staff s ON sd.staff_id = s.staff_id
          WHERE sd.status = 'pending'";

$params = [];
$types = "";

if (!empty($searchTerm)) {
    $query .= " AND (sd.staff_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
    $types .= "sss";
    $likeSearch = "%$searchTerm%";
    $params = [$likeSearch, $likeSearch, $likeSearch];
}

$query .= " ORDER BY sd.id DESC";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$disputes = [];
while ($row = $result->fetch_assoc()) {
    if ($filterMonth && !empty($row['payout_date'])) {
        $payoutMonth = date('F', strtotime($row['payout_date']));
        if ($payoutMonth != $filterMonth) continue;
    }
    $disputes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Salary Disputes - Hotel La Vista</title>
<link rel="stylesheet" href="salary_dispute.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
        <div class="profile-header">
            <h1>Pending Salary Disputes</h1>
            <div class="tools-row">
                <a href="../payroll.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Back</a>
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
                <a href="dispute_history.php" class="nav-btn"><i class="fas fa-history"></i> View All History</a>
            </div>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Employee</th>
                        <th>Payout Date</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Date Filed</th>
                        <th>Proof</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($disputes)): ?>
                        <?php foreach($disputes as $d): ?>
                        <tr>
                            <td><?= $d['id'] ?></td>
                            <td><?= htmlspecialchars($d['staff_id'].' - '.$d['first_name'].' '.$d['last_name']) ?></td>
                            <td><?= htmlspecialchars($d['payout_date']) ?></td>
                            <td><?= number_format((float)$d['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($d['reason']) ?></td>
                            <td><?= date('F j, Y', strtotime($d['date_filed'])) ?></td>
                            <td>
                                <?php if(!empty($d['proof_file']) && file_exists($_SERVER['DOCUMENT_ROOT'].$disputePath.basename($d['proof_file']))): ?>
                                    <a href="<?= $disputePath.urlencode(basename($d['proof_file'])) ?>" target="_blank" class="proof-link">View</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btn-wrapper">
                                    <form method="post" action="process_dispute.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="action-btn approve"><i class="fas fa-check"></i> Approve</button>
                                    </form>
                                    <form method="post" action="process_dispute.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="action-btn reject"><i class="fas fa-times"></i> Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8">No pending salary disputes found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
