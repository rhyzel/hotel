<?php
include '../../db.php';

$staffFilter = $_GET['staff_id'] ?? '';

$bonusesQuery = "
    SELECT b.*, CONCAT(s.first_name, ' ', s.last_name) AS fullname
    FROM bonuses_incentives b
    JOIN staff s ON b.staff_id = s.staff_id
";
if($staffFilter) {
    $bonusesQuery .= " WHERE b.staff_id = '". $conn->real_escape_string($staffFilter) ."'";
}
$bonusesQuery .= " ORDER BY b.created_at DESC";

$bonusesResult = $conn->query($bonusesQuery);

$bonuses = [];
if ($bonusesResult && $bonusesResult->num_rows > 0) {
    while ($row = $bonusesResult->fetch_assoc()) {
        $bonuses[] = $row;
    }
}

$staffResult = $conn->query("SELECT staff_id, first_name, last_name FROM staff WHERE staff_id IS NOT NULL ORDER BY first_name");

$totalAmount = 0;
foreach($bonuses as $b) {
    $totalAmount += $b['amount'];
}

$message = '';
if(isset($_POST['submit'])) {
    $staff_id = $_POST['staff_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];

    $checkStaff = $conn->prepare("SELECT 1 FROM staff WHERE staff_id = ?");
    $checkStaff->bind_param("s", $staff_id);
    $checkStaff->execute();
    $checkStaff->store_result();

    if($checkStaff->num_rows > 0){
        $stmt = $conn->prepare("INSERT INTO bonuses_incentives (staff_id, type, amount, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("ssd", $staff_id, $type, $amount);
        if($stmt->execute()){
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $message = 'Error adding bonus/incentive.';
        }
    } else {
        $message = 'Selected employee does not exist.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bonuses & Incentives - Hotel La Vista</title>
<link rel="stylesheet" href="bonuses_incentives.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
  <header class="page-header">
    <h2>Staff Bonus & Incentives</h2>
    <p class="total-amount">Total: <?= number_format($totalAmount, 2) ?></p>

    <div class="header-controls">
      <a href="http://localhost/hotel/hr/payroll/payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>

      <form method="get" class="filter-form">
        <select name="staff_id" class="staff-select">
          <option value="">All Employees</option>
          <?php 
          $staffResult->data_seek(0);
          while($staff = $staffResult->fetch_assoc()): ?>
            <option value="<?= $staff['staff_id'] ?>" <?= ($staffFilter == $staff['staff_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($staff['first_name'].' '.$staff['last_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
        <button type="submit" class="edit-btn"><i class="fas fa-filter"></i> Filter</button>
      </form>

      <button class="edit-btn" id="openModalBtn"><i class="fas fa-plus"></i> Add Bonus/Incentive</button>

      <form method="get" action="export_bonuses.php" style="margin:0;">
    <input type="hidden" name="staff_id" value="<?= htmlspecialchars($staffFilter) ?>">
    <button type="submit" class="edit-btn"><i class="fas fa-file-export"></i> Export</button>
</form>

  </header>


    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Employee</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if (!empty($bonuses)): ?>
            <?php foreach ($bonuses as $b): ?>
                <tr>
                    <td>
                      <a href="staff_bonuses.php?staff_name=<?= urlencode($b['fullname']) ?>" class="staff-link">
                          <?= htmlspecialchars($b['fullname']) ?>
                      </a>
                    </td>
                    <td><?= htmlspecialchars($b['type']) ?></td>
                    <td><?= number_format($b['amount'], 2) ?></td>
                    <td><?= date('F j, Y', strtotime($b['created_at'])) ?></td>
                    <td>
                        <div class="action-buttons">
                            <a href="bonuses_incentives_edit.php?id=<?= $b['id'] ?>" class="edit-btn"><i class="fas fa-edit"></i> Edit</a>
                            <a href="bonuses_incentives_delete.php?id=<?= $b['id'] ?>" class="delete-btn" onclick="return confirmDelete();"><i class="fas fa-trash"></i> Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">No bonuses or incentives recorded.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <div class="form-container">
        <h2>Add Bonus/Incentive</h2>
        <?php if($message): ?>
            <p class="message"><?= $message ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="staff_id">Employee:</label>
            <select name="staff_id" id="staff_id" required>
                <option value="">Select Employee</option>
                <?php 
                $staffResult->data_seek(0);
                while($staff = $staffResult->fetch_assoc()): ?>
                    <option value="<?= $staff['staff_id'] ?>"><?= htmlspecialchars($staff['first_name'].' '.$staff['last_name']) ?></option>
                <?php endwhile; ?>
            </select>
            <label for="type">Type:</label>
            <select name="type" id="type" required>
                <option value="">Select Type</option>
                <option value="Bonus">Bonus</option>
                <option value="Incentive">Incentive</option>
            </select>
            <label for="amount">Amount:</label>
            <input type="number" name="amount" id="amount" step="0.01" required>
            <button type="submit" name="submit" class="edit-btn">Add Bonus/Incentive</button>
        </form>
    </div>
  </div>
</div>

<script>
const modal = document.getElementById('modal');
const openBtn = document.getElementById('openModalBtn');
const closeBtn = document.getElementById('closeModalBtn');
openBtn.onclick = () => modal.style.display = 'flex';
closeBtn.onclick = () => modal.style.display = 'none';
window.onclick = e => { if(e.target === modal) modal.style.display = 'none'; }

function confirmDelete() {
    return confirm('Are you sure you want to delete this bonus/incentive?');
}
</script>
</body>
</html>
