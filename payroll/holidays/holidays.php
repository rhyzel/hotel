<?php
include '../db.php';

$holidayResult = $conn->query("SELECT * FROM holidays ORDER BY date ASC");
$holidays = [];
if ($holidayResult && $holidayResult->num_rows > 0) {
    while ($row = $holidayResult->fetch_assoc()) {
        $holidays[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Holidays - Hotel La Vista</title>
<link rel="stylesheet" href="holidays.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="overlay">
    <div class="container">
        <h2>Upcoming Holidays</h2>
        <form action="save_holiday_percentage.php" method="post">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Holiday Name</th>
                        <th>Date</th>
                        <th>Pay Percentage (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($holidays)): ?>
                        <?php foreach($holidays as $h): ?>
                            <tr>
                                <td><?= $h['id'] ?></td>
                                <td><?= htmlspecialchars($h['name']) ?></td>
                                <td><?= date('F j, Y', strtotime($h['date'])) ?></td>
                                <td>
                                    <input type="number" name="percentage[<?= $h['id'] ?>]" value="<?= $h['percentage'] ?? 100 ?>" min="0" max="200" step="0.1" required>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">No holidays found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="button-group">
                <a href="http://localhost/hotel/hr/payroll/payroll.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
