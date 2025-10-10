<?php
include '../db.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    header('Location: dispute_history.php');
    exit;
}

$stmt = $conn->prepare("SELECT * FROM salary_dispute WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$dispute = $result->fetch_assoc();

if (!$dispute) {
    header('Location: dispute_history.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payout_date = $_POST['payout_date'];
    $amount = $_POST['amount'];
    $reason = $_POST['reason'];
    $status = $_POST['status'];

    $update = $conn->prepare("UPDATE salary_dispute SET payout_date = ?, amount = ?, reason = ?, status = ? WHERE id = ?");
    $update->bind_param('sdssi', $payout_date, $amount, $reason, $status, $id);
    $update->execute();

    header('Location: dispute_history.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Dispute #<?= htmlspecialchars($id) ?> - Hotel La Vista</title>
<link rel="stylesheet" href="salary_dispute.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
        <div class="profile-header">
            <h1>Edit Dispute #<?= htmlspecialchars($id) ?></h1>
            <div class="tools-row">
                <a href="dispute_history.php" class="nav-btn"><i class="fas fa-arrow-left"></i> Back to History</a>
            </div>
        </div>

        <div class="table-wrapper" style="max-width:600px;margin:0 auto;">
            <form method="post" class="filter-form" style="flex-direction:column;gap:15px;">
                <label>
                    <strong>Payout Date:</strong><br>
                    <input type="date" name="payout_date" value="<?= htmlspecialchars($dispute['payout_date']) ?>" required>
                </label>

                <label>
                    <strong>Amount:</strong><br>
                    <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($dispute['amount']) ?>" required>
                </label>

                <label>
                    <strong>Reason:</strong><br>
                    <textarea name="reason" rows="4" style="width:100%;padding:8px;border:2px solid #4B5320;border-radius:6px;background:#FFFCE3;color:#4B5320;"><?= htmlspecialchars($dispute['reason']) ?></textarea>
                </label>

                <label>
                    <strong>Status:</strong><br>
                    <select name="status" required>
                        <option value="pending" <?= $dispute['status']=='pending'?'selected':'' ?>>Pending</option>
                        <option value="approved" <?= $dispute['status']=='approved'?'selected':'' ?>>Approved</option>
                        <option value="rejected" <?= $dispute['status']=='rejected'?'selected':'' ?>>Rejected</option>
                    </select>
                </label>

                <button type="submit" class="nav-btn" style="width:100%;justify-content:center;">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
