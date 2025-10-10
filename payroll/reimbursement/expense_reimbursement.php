<?php
include '../db.php';

if (isset($_GET['id'], $_GET['action'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } else {
        header("Location: expense_reimbursement.php");
        exit();
    }

    $stmt = $conn->prepare("UPDATE reimbursements SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: expense_reimbursement.php");
    exit();
}

$search = $_GET['search'] ?? '';
$query = "SELECT r.id, r.amount, r.reason, r.notes, r.submitted_at, r.proof_file, r.status, CONCAT(s.first_name, ' ', s.last_name) AS staff_name 
          FROM reimbursements r 
          LEFT JOIN staff s ON r.staff_id = s.staff_id 
          WHERE r.status = 'Pending'";

if (!empty($search)) {
    $query .= " AND (s.first_name LIKE ? OR s.last_name LIKE ?)";
    $stmt = $conn->prepare($query);
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Reimbursement Requests - Hotel La Vista</title>
<link rel="stylesheet" href="expense_reimbursement.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header class="page-header">
      <h2>Pending Reimbursement Requests</h2>
      <div class="header-controls">
        <a href="http://localhost/hotel/hr/payroll/payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>
        <form method="get" class="filter-form">
          <input type="text" name="search" placeholder="Search employee..." value="<?= htmlspecialchars($search); ?>">
          <button type="submit">Search</button>
        </form>
        <form method="post" action="export_reimbursements.php" class="export-form">
          <button type="submit" class="nav-btn"><i class="fas fa-file-csv"></i> Export</button>
        </form>
        <a href="all_reimbursement_history.php" class="nav-btn"><i class="fas fa-history"></i> View History</a>
      </div>
    </header>

    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Employee</th>
            <th>Amount</th>
            <th>Reason</th>
            <th>Notes</th>
            <th>Submitted At</th>
            <th>Proof</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if($result && mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['staff_name']) ?></td>
                <td>₱<?= number_format($row['amount'],2) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['notes'] ?? '') ?></td>
                <td><?= date('M d, Y', strtotime($row['submitted_at'])) ?></td>
                <td>
                  <?php if($row['proof_file']): 
                    $fileName = basename($row['proof_file']);
                    $fileUrl = "http://localhost/hotel/hr/payroll/uploads/reimbursements/" . urlencode($fileName);
                  ?>
                    <a href="<?= $fileUrl ?>" target="_blank">View</a>
                  <?php else: ?>
                    N/A
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['status']); ?></td>
                <td>
                  <div class="action-btn-wrapper">
                    <a href="expense_reimbursement.php?id=<?= $row['id'] ?>&action=approve" class="action-btn approve">Approve</a>
                    <a href="expense_reimbursement.php?id=<?= $row['id'] ?>&action=reject" class="action-btn reject">Reject</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="9">No pending reimbursements found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
