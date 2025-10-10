<?php
include '../../db.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id <= 0) {
    header("Location: bonuses_incentives.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM bonuses_incentives WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$bonus = $result->fetch_assoc();

if(!$bonus){
    header("Location: bonuses_incentives.php");
    exit;
}

$staffResult = $conn->query("SELECT staff_id, first_name, last_name FROM staff ORDER BY first_name");

$message = '';

if(isset($_POST['submit'])){
    $staff_id = $_POST['staff_id'];
    $type = $_POST['type'];
    $amount = $_POST['amount'];

    $checkStaff = $conn->prepare("SELECT 1 FROM staff WHERE staff_id = ?");
    $checkStaff->bind_param("s", $staff_id);
    $checkStaff->execute();
    $checkStaff->store_result();

    if($checkStaff->num_rows > 0){
        $update = $conn->prepare("UPDATE bonuses_incentives SET staff_id=?, type=?, amount=? WHERE id=?");
        $update->bind_param("ssdi", $staff_id, $type, $amount, $id);
        if($update->execute()){
            header("Location: bonuses_incentives.php");
            exit;
        } else {
            $message = "Error updating bonus/incentive.";
        }
    } else {
        $message = "Selected employee does not exist.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Bonus/Incentive</title>
<link rel="stylesheet" href="bonuses_incentives_edit.css">
</head>
<body>
<div class="overlay">
  <div class="container">
    <h2>Edit Bonus/Incentive</h2>
    <?php if($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="staff_id">Employee:</label>
        <select name="staff_id" id="staff_id" required>
            <option value="">Select Employee</option>
            <?php while($staff = $staffResult->fetch_assoc()): ?>
                <option value="<?= $staff['staff_id'] ?>" <?= $staff['staff_id']==$bonus['staff_id']?'selected':'' ?>>
                    <?= htmlspecialchars($staff['first_name'].' '.$staff['last_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label for="type">Type:</label>
        <select name="type" id="type" required>
            <option value="">Select Type</option>
            <option value="Bonus" <?= $bonus['type']=='Bonus'?'selected':'' ?>>Bonus</option>
            <option value="Incentive" <?= $bonus['type']=='Incentive'?'selected':'' ?>>Incentive</option>
        </select>
        <label for="amount">Amount:</label>
        <input type="number" name="amount" id="amount" step="0.01" value="<?= htmlspecialchars($bonus['amount']) ?>" required>
        <button type="submit" name="submit" class="edit-btn">Update</button>
        <a href="bonuses_incentives.php" class="nav-btn">Cancel</a>
    </form>
  </div>
</div>
</body>
</html>
