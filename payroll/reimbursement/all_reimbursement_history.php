<?php
include '../db.php';

$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? 'All';

$query = "SELECT r.*, CONCAT(s.first_name,' ',s.last_name) AS staff_name, s.manager 
          FROM reimbursements r
          JOIN staff s ON r.staff_id = s.staff_id
          WHERE 1=1";

$params = [];
$types = "";

if (in_array($filter, ['Approved','Rejected'])) {
    $query .= " AND r.status = ?";
    $types .= "s";
    $params[] = $filter;
}

if (!empty($search)) {
    $query .= " AND (s.staff_id LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ?)";
    $types .= "sss";
    $likeSearch = "%$search%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $params[] = $likeSearch;
}

$query .= " ORDER BY r.submitted_at DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reimbursement History - Hotel La Vista</title>
<link rel="stylesheet" href="expense_reimbursement.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="overlay">
  <div class="container">
    <header class="page-header">
      <h2>Reimbursement History</h2>
      <div class="header-controls">
        <a href="http://localhost/hotel/hr/payroll/reimbursement/expense_reimbursement.php" class="nav-btn">&#8592; Back</a>
        <form method="get" class="filter-form">
          <input type="text" name="search" placeholder="Search employee..." value="<?= htmlspecialchars($search); ?>">
          <select name="filter">
            <option value="All" <?= $filter=='All' ? 'selected' : '' ?>>All</option>
            <option value="Approved" <?= $filter=='Approved' ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= $filter=='Rejected' ? 'selected' : '' ?>>Rejected</option>
          </select>
          <button type="submit">Search</button>
        </form>
        <form method="post" action="export_reimbursements.php" class="export-form">
          <button type="submit" class="nav-btn"><i class="fas fa-file-csv"></i> Export</button>
        </form>
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
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8">No reimbursement history found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
