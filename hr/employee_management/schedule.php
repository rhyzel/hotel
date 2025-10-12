<?php
require_once('../db.php');
session_start();

if(isset($_POST['update_schedule'])){
    $staff_id = $_POST['staff_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $conn->prepare("UPDATE staff SET schedule_start_time = ?, schedule_end_time = ? WHERE staff_id = ?");
    $stmt->bind_param("sss", $start_time, $end_time, $staff_id);
    $stmt->execute();
    header("Location: schedule.php");
    exit;
}

$employees = $conn->query("SELECT staff_id, first_name, last_name, schedule_start_time, schedule_end_time FROM staff ORDER BY first_name ASC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Schedule</title>
    <link rel="stylesheet" href="../css/schedule.css">
</head>
<body>
    <div class="container">
        <a href="attendance.php" class="back-button">&larr; Back</a>
        <h1>Employee Schedule</h1>
        <table>
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Schedule Start</th>
                    <th>Schedule End</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($employees as $emp): ?>
                <tr>
                    <form method="POST">
                        <td data-label="Staff ID"><?= $emp['staff_id'] ?></td>
                        <td data-label="Name"><?= $emp['first_name'] . ' ' . $emp['last_name'] ?></td>
                        <td data-label="Schedule Start">
                            <input type="time" name="start_time" value="<?= $emp['schedule_start_time'] ?>" class="time-input" required>
                        </td>
                        <td data-label="Schedule End">
                            <input type="time" name="end_time" value="<?= $emp['schedule_end_time'] ?>" class="time-input" required>
                        </td>
                        <td data-label="Action">
                            <input type="hidden" name="staff_id" value="<?= $emp['staff_id'] ?>">
                            <button type="submit" name="update_schedule" class="update-btn">Update</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
